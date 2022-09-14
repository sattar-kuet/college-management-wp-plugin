<?php
//admin_url('admin.php?page=itscholarbd_student_subject&action=add')
global $wpdb;



if(isset($_POST['submit_btn'])){

  $subject_ids = $_POST['subject_ids'];
  $data = $_POST;
  //print_r($data); exit;
  global $wpdb;
  $table = $wpdb->prefix.'exam_configuration';

  for($i=0; $i < count($subject_ids); $i++){

        $sql = "SELECT * FROM ".$table." WHERE exam_id=".$data['exam_id'];
        $sql .= " AND subject_id=".$subject_ids[$i];
        $old_record = $wpdb->get_results($sql);
        if(count($old_record)){
          $id = $old_record[0]->id;
          $sql = "UPDATE $table SET ";
          $sql .=" mcq_mark=".$data['mcq_mark'][$i];
          $sql .=", mcq_pass_mark=".$data['mcq_pass_mark'][$i];
          $sql .=", written_mark=".$data['written_mark'][$i];
          $sql .=", written_pass_mark=".$data['written_pass_mark'][$i];
          $sql .=" WHERE id=".$id;
          //echo $sql; exit;
          $wpdb->query($wpdb->prepare($sql));
        }
        else{
          $formatted_data = [];
          $formatted_data['exam_id'] = $data['exam_id'];
          $formatted_data['subject_id'] = $subject_ids[$i];  
          $formatted_data['mcq_mark'] = $data['mcq_mark'][$i];  
          $formatted_data['mcq_pass_mark'] = $data['mcq_pass_mark'][$i];  
          $formatted_data['written_mark'] = $data['written_mark'][$i];  
          $formatted_data['written_pass_mark'] = $data['written_pass_mark'][$i];  
         // print_r($formatted_data); exit;
          $result = $wpdb->insert($table,$formatted_data);
        }
        
  }
}
$sql = "SELECT * FROM ".$wpdb->prefix."exam WHERE id =".$_GET['id'];
$exam = $wpdb->get_results($sql)[0];

$sql = "SELECT student.id as student_id, student.name as student_name, student.roll as student_roll, student.group_name as student_group, student.session_start as student_session_start, student.session_end as student_session_end, subject.name as subject_name, student_subject.subject_type as subject_type FROM ".$wpdb->prefix."student as student ";

$sql .= " LEFT JOIN wp_student_subject as student_subject ON student.id = student_subject.student_id";
$sql .= " LEFT JOIN wp_subject as subject ON student_subject.id = subject.id";
$sql .=" WHERE student.session_start =".$exam->session_start." AND student.session_end=".$exam->session_end;
$student_subjects = $wpdb->get_results($sql);

$student_subject_list = [];
$index = -1;
$student_enlisted = [];
foreach($student_subjects as $student_subject){
  if(!isset($student_enlisted[$student_subject->student_id])){
    $index++;
  }
   $student_subject_list[$index]['student'] = [
                                         'name' => $student_subject->student_name,
                                         'roll' => $student_subject->student_roll,
                                         'group' => $student_subject->student_group,
                                         'session' => $student_subject->student_session_start.'-'.$student_subject->student_session_end,
                                   ];
   if($student_subject->subject_type != -1 && !empty($student_subject->subject_name)){
     $student_subject_list[$index]['subjects'][] = [
                                         'name' => $student_subject->subject_name,
                                         'type' => $student_subject->subject_type
                                        
                                   ];
    $student_enlisted[$student_subject->student_id] = 1;

   }   
}

//echo '<pre>'; print_r($student_subject_list); exit;

$sql = "SELECT * FROM ".$wpdb->prefix."exam_configuration WHERE exam_id=".$_GET['id'];
$exam_configurations = $wpdb->get_results($sql);
//echo $sql; exit;
$active_record = [];
foreach($exam_configurations as $exam_configuration){
  $active_record[$exam_configuration->subject_id]['mcq_mark'] = $exam_configuration->mcq_mark;
  $active_record[$exam_configuration->subject_id]['mcq_pass_mark'] = $exam_configuration->mcq_pass_mark;
  $active_record[$exam_configuration->subject_id]['written_mark'] = $exam_configuration->written_mark;
  $active_record[$exam_configuration->subject_id]['written_pass_mark'] = $exam_configuration->written_pass_mark;
}
//print_r($active_record); exit;
 $sql = "SELECT * FROM ".$wpdb->prefix."exam WHERE id=".$_GET['id'];
 $exam = $wpdb->get_results($sql)[0]; 
// print_r($exam); exit;
?>
<div class="wrap">
  <h2 style="margin-bottom: 20px;">Input Mark for <?php echo $exam->name.': '.$exam->session_start.'-'.$exam->session_end; ?> </h2> 
  
  <form method="post" name="add_student_form">
    <input type="hidden" name="exam_id" value="<?php echo $exam->id; ?>">
        <table>
               
                <?php 
                foreach($student_subject_list as $student_subject){ ?>

                  <tr>
                    <td><?php echo $student_subject['student']['name'].' '.$student_subject['student']['roll'].' '.$student_subject['student']['group'].' '. $student_subject['student']['session']; ?>
                    </td>
                  </tr>

                  <?php if(isset($student_subject['subjects'] )){ ?>

                      <?php foreach($student_subject['subjects'] as $subject){ ?>

                        <tr>
                            <td>
                              <input type="text"  value="<?php echo $subject['name']; ?>" readonly>
                              <input type="text"  name="mcq_mark" placeholder="MCQ mark">
                              <input type="text"  name="written_mark" placeholder="Written mark">
                            </td>   
                        </tr> 

                      <?php } ?>

                  <?php } else{
                    echo '<tr><td style="color: red;">No Subject configured for this student</td></tr>';
                  }?>

              <?php  } ?>
                
                <tr>
                        <td colspan="2">
                                <button type="submit" name="submit_btn" class="button-primary" style="margin-top: 20px; float: right;"> Submit</button>
                        </td>
                </tr>       
        </table>
        
  </form>
</div>

gn0w+S#A8Tzw