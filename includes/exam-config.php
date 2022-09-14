<?php

//admin_url('admin.php?page=itscholarbd_student_subject&action=add')

global $wpdb;

$sql = "SELECT * FROM ".$wpdb->prefix."subject";
$subjects = $wpdb->get_results($sql);

//print_r($subjects); exit;

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
  <h2 style="margin-bottom: 20px;">Exam configuration for </h2> 
  <h3><?php echo $exam->name.' : '.$exam->session_start.'-'.$exam->session_end; ?></h3>
  <form method="post" name="add_student_form">
    <input type="hidden" name="exam_id" value="<?php echo $exam->id; ?>">
        <table>
               
                <?php 
                foreach($subjects as $subject){ ?>
                  <tr>
                        <td>
                          <input type="hidden" name="subject_ids[]" value="<?php echo $subject->id?>">
                          <input type="text"  value="<?php echo $subject->name?>" readonly>
                        </td>

                        <td>
                         <input type="text" name="mcq_mark[]" placeholder="MCQ mark" 
                          value="<?php echo $active_record[$subject->id]['mcq_mark'];?>">
                       </td>
                       <td>
                         <input type="text" name="mcq_pass_mark[]" placeholder="MCQ pass mark"
                         value="<?php echo $active_record[$subject->id]['mcq_pass_mark'];?>">
                       </td>
                       <td>
                         <input type="text" name="written_mark[]" placeholder="Written mark"
                         value="<?php echo $active_record[$subject->id]['written_mark'];?>" >
                       </td>
                       <td>
                         <input type="text" name="written_pass_mark[]" placeholder="Written pass mark"
                         value="<?php echo $active_record[$subject->id]['written_pass_mark'];?>" >
                          
                        </td>
                        
                </tr> 

              <?php  } ?>
                
                <tr>
                        <td colspan="2">
                                <button type="submit" name="submit_btn" class="button-primary" style="margin-top: 20px; float: right;"> Submit</button>
                        </td>
                </tr>       
        </table>
        
  </form>
</div>