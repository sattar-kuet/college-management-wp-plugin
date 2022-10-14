<?php
global $wpdb;
if(isset($_POST['submit_btn'])){

  $subject_ids = $_POST['subject_ids'];
  $data = $_POST;
  //echo '<pre>';print_r($data); exit;
  global $wpdb;
  $table = $wpdb->prefix.'student_result';

  foreach($data['student_ids'] as $key=>$student_id){

    $group_name = $data['student_groups'][$key];
    foreach($data['subject'][$student_id] as $subject_id){
       $sql = "SELECT * FROM ".$table." WHERE exam_id=".$data['exam_id'];
        $sql .= " AND student_id=".$student_id;
        $sql .= " AND subject_id=".$subject_id;
        $old_record = $wpdb->get_results($sql);
        if(count($old_record)){
          $id = $old_record[0]->id;
          $mcq_mark = $data['mcq'][$student_id][$subject_id];
          if(empty($data['mcq'][$student_id][$subject_id])){
            $mcq_mark = 0;
          }
          $written_mark = $data['written'][$student_id][$subject_id];
          if(empty($data['written'][$student_id][$subject_id])){
            $written_mark = 0;
          }
          $practical_mark = $data['practical'][$student_id][$subject_id];
          if(empty($data['practical'][$student_id][$subject_id])){
            $practical_mark = 0;
          }
          $sql = "UPDATE $table SET ";
          $sql .=" mcq_mark=".$mcq_mark;
          $sql .=", written_mark=".$written_mark;
          $sql .=", practical_mark=".$practical_mark;
          $sql .=" WHERE id=".$id;
          //echo $sql; exit;
          $wpdb->query($wpdb->prepare($sql));
        }else{
          $formatted_data = [];
          $formatted_data['exam_id'] = $data['exam_id'];
          $formatted_data['group_name'] = $group_name;
          $formatted_data['student_id'] = $student_id;
          $formatted_data['subject_id'] = $subject_id;  
          $formatted_data['subject_parent_id'] = $data['parent_subject_id'][$student_id][$subject_id];   
          $formatted_data['subject_type'] = $data['subject_type'][$student_id][$subject_id];  
          $formatted_data['mcq_mark'] = $data['mcq'][$student_id][$subject_id];   
          $formatted_data['written_mark'] = $data['written'][$student_id][$subject_id];
          $formatted_data['practical_mark'] = $data['practical'][$student_id][$subject_id];
          //print_r($formatted_data); exit;
          $result = $wpdb->insert($table,$formatted_data);
        }
    }
  }
}
$sql = "SELECT * FROM ".$wpdb->prefix."exam WHERE id =".$_GET['id'];
$exam = $wpdb->get_results($sql)[0];

$sql = "SELECT 
               student.id as student_id, 
               student_subject.id as student_subject_id, 
               student.name as student_name, 
               student.roll as student_roll, 
               student.group_name as student_group, 
               student.session_start as student_session_start, 
               student.session_end as student_session_end, 
               subject.id as subject_id, 
               subject.parent_id as subject_parent_id, 
               subject.name as subject_name, 
               student_subject.subject_id as subject_id, 
               student_subject.subject_type as subject_type 
               FROM ".$wpdb->prefix."student as student ";

$sql .= " LEFT JOIN wp_student_subject as student_subject ON student.id = student_subject.student_id";
$sql .= " LEFT JOIN wp_subject as subject ON student_subject.subject_id = subject.id";
$sql .=" WHERE student.session_start =".$exam->session_start." AND student.session_end=".$exam->session_end;
$student_subjects = $wpdb->get_results($sql);
//echo $sql;
//echo'<pre>'; print_r($student_subjects);
$student_subject_list = [];
$index = -1;
$student_enlisted = [];
foreach($student_subjects as $student_subject){
  if(!isset($student_enlisted[$student_subject->student_id])){
    $index++;
  }
   $student_subject_list[$index]['student'] = [
                                         'id' => $student_subject->student_id,
                                         'name' => $student_subject->student_name,
                                         'roll' => $student_subject->student_roll,
                                         'group' => $student_subject->student_group,
                                         'session' => $student_subject->student_session_start.'-'.$student_subject->student_session_end,
                                   ];

   if($student_subject->subject_type != -1 && !empty($student_subject->subject_name
    && $_GET['subject_id'] == $student_subject->subject_id)){
     $student_subject_list[$index]['subjects'][$student_subject->subject_id] = [
                                         'id' => $student_subject->subject_id,
                                         'parent_id' => $student_subject->subject_parent_id,
                                         'name' => $student_subject->subject_name,
                                         'type' => $student_subject->subject_type   
                                   ];
   }   
    $student_enlisted[$student_subject->student_id] = 1;
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
  $active_record[$exam_configuration->subject_id]['practical_mark'] = $exam_configuration->practical_mark;
  $active_record[$exam_configuration->subject_id]['practical_pass_mark'] = $exam_configuration->practical_pass_mark;
}
//print_r($active_record); exit;
 $sql = "SELECT * FROM ".$wpdb->prefix."exam WHERE id=".$_GET['id'];
 $exam = $wpdb->get_results($sql)[0]; 
// print_r($exam); exit;
 $sql = "SELECT * FROM ".$wpdb->prefix."student_result WHERE exam_id=".$_GET['id'];
 $exam_results = $wpdb->get_results($sql); 
 $mcq_mark=[]; 
 $written_mark=[]; 
 $practical_mark=[]; 
 foreach($exam_results as $exam_result){

     $mcq_mark[$exam_result->student_id][$exam_result->subject_id] = $exam_result->mcq_mark;
     $written_mark[$exam_result->student_id][$exam_result->subject_id] = $exam_result->written_mark;
     $practical_mark[$exam_result->student_id][$exam_result->subject_id] = $exam_result->practical_mark;
 }

// echo '<pre>'; print_r($mcq_mark); 

  $sql = "SELECT subject.id as subject_id, subject.name as subject_name FROM ".$wpdb->prefix."exam_configuration as exam_config";
  $sql .=" LEFT JOIN wp_subject as subject ON exam_config.subject_id = subject.id";
  $sql .=" WHERE subject.has_two_part = 0 AND (exam_config.mcq_mark !=0 OR exam_config.written_mark != 0 OR exam_config.practical_mark != 0)";
 $subjects = $wpdb->get_results($sql);
 $urls = [];
 $current_url = admin_url('admin.php?page='.$_GET['page'].'&action='.$_GET['action'].'&id='.$_GET['id']);
 
 foreach($subjects as $subject){
    $urls[] = [
      'href'=>$current_url.'&subject_id='.$subject->subject_id,
      'text'=>$subject->subject_name,
      'subject_id'=>$subject->subject_id
    ];
 }
?>
<div class="wrap">
 <h2 style="margin-bottom: 20px;">Input Marks for <?php echo $exam->name.': '.$exam->session_start.'-'.$exam->session_end; ?> </h2> 
<form action="" method="get">
    <?php 
     foreach($urls as $url) { ?>
         <a class ="custom_link <?php if ($_GET['subject_id'] == $url['subject_id']) echo 'active_link';?>" href="<?php echo $url['href']; ?>"><?php echo $url['text']; ?></a>
     <?php } ?>
</form>
 
<?php if (isset($_GET['subject_id'])) { ?>
  <form method="post" name="add_student_form">
    <input type="hidden" name="exam_id" value="<?php echo $exam->id; ?>">
        <table>
               <tr>
                  <th>Student Name</th>
                  <th>
                    <?php 
                     $subject_id_from_url = $_GET['subject_id'];

                    if($active_record[$subject_id_from_url]['mcq_mark'] != 0){?>
                      MCQ Marks
                    <?php }?>
                  </th>
                  <th>
                 <?php if($active_record[$subject_id_from_url]['written_mark'] != 0){?>
                      Written Marks
                    <?php }?>
                  </th>
                  <th>
                 <?php if($active_record[$subject_id_from_url]['practical_mark'] != 0){?>
                      Practical Marks
                    <?php }?>
                  </th>
               </tr>
                <?php 
                foreach($student_subject_list as $student_subject){ ?>

                  <tr>
                    <td colspan="2">
                     <input type="hidden"  name="student_ids[]" value="<?php echo $student_subject['student']['id']; ?>">
                      <input type="hidden"  name="student_groups[]" value="<?php echo $student_subject['student']['group']; ?>">
                    <?php $student_info =  $student_subject['student']['roll'].' '.$student_subject['student']['name'].' '.$student_subject['student']['group']; ?>
                   </td>
                    
                  </tr>

                  <?php if(isset($student_subject['subjects'] )){ ?>

                      <?php foreach($student_subject['subjects'] as $subject){ 
                          $student_id = $student_subject['student']['id'];
                          $subject_id = $subject['id'];
                          $subject_name_attr = "subject[".$student_subject['student']['id']."][]"; 
                          $subject_type_name_attr = "subject_type[".$student_id."][".$subject_id."]"; 
                          $parent_subject_name_attr = "parent_subject_id[".$student_id."][".$subject_id."]"; 
                         
                          $mcq_name_attr = "mcq[".$student_id."][".$subject_id."]";
                          $written_name_attr = "written[".$student_id."][".$subject_id."]";
                          $practical_name_attr = "practical[".$student_id."][".$subject_id."]";
                        ?>

                        <tr>
                            <td>
                              
                              <input type="hidden"  name="<?php echo $subject_name_attr; ?>" value="<?php echo $subject['id']; ?>">
                              
                              <input type="hidden"  name="<?php echo $subject_type_name_attr; ?>" value="<?php echo $subject['type']; ?>">

                              <input type="hidden"  name="<?php echo $parent_subject_name_attr; ?>" value="<?php echo $subject['parent_id']; ?>">
                              
                              <input type="text"  value="<?php echo $student_info; ?>" readonly>
                            </td>
                            <td>
                            <?php if($active_record[$subject_id]['mcq_mark'] != 0){?>
                            
                              <input type="number" step="0.01" max="<?php echo $active_record[$subject_id]['mcq_mark'];?>" name="<?php echo $mcq_name_attr; ?>" placeholder="MCQ mark"
                              <?php if(isset($mcq_mark[$student_id][$subject_id])) {?> value="<?php echo $mcq_mark[$student_id][$subject_id];?>" <?php } ?>>
                            
                          <?php }else{?>
                            <input type="hidden"  name="<?php echo $mcq_name_attr; ?>" value="0">

                          <?php } ?>
                          </td>
                            <td>
                              <?php if($active_record[$subject_id]['written_mark'] != 0){?>
                              <input type="number" step="0.01" max="<?php echo $active_record[$subject_id]['written_mark'];?>"  name="<?php echo $written_name_attr; ?>" placeholder="Written mark" <?php if(isset($written_mark[$student_id][$subject_id])) {?> value="<?php echo $written_mark[$student_id][$subject_id];?>" <?php } ?>>
                               <?php }else{?>
                            <input type="hidden"  name="<?php echo $written_name_attr; ?>" value="0">

                          <?php } ?>
                            </td>   
                            <td>
                              <?php if($active_record[$subject_id]['practical_mark'] != 0){?>
                              <input type="number" step="0.01" max="<?php echo $active_record[$subject_id]['practical_mark'];?>"  name="<?php echo $practical_name_attr; ?>" placeholder="practical mark" <?php if(isset($practical_mark[$student_id][$subject_id])) {?> value="<?php echo $practical_mark[$student_id][$subject_id];?>" <?php } ?>>
                               <?php }else{?>
                            <input type="hidden"  name="<?php echo $practical_name_attr; ?>" value="0">

                          <?php } ?>
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
<?php } ?>
</div>

<style type="text/css">
  .custom_link{
    text-decoration: none;
    display: inline-block;
    background: #3c434a;
    padding: 4px 10px;
    color: #FFF;
    margin: 10px;
  }
  .active_link {
    background: #b97906;
}
</style>