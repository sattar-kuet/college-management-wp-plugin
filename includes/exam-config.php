<?php
 require plugin_dir_path( __FILE__ ) . 'utility.php';

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

$active_record = exam_config($_GET['id']);
//print_r($active_record); exit;
 $sql = "SELECT * FROM ".$wpdb->prefix."exam WHERE id=".$_GET['id'];
 $exam = $wpdb->get_results($sql)[0]; 
// print_r($exam); exit;
?>
<div class="wrap">
  <h2 style="margin-bottom: 20px;">Exam configuration for <span style="color: green;"><?php echo $exam->name.' : '.$exam->session_start.'-'.$exam->session_end; ?></span></h2> 
  
  <form method="post" name="add_student_form">
    <input type="hidden" name="exam_id" value="<?php echo $exam->id; ?>">
        <table>
               <tr>
                 <th>Subject Name</th>
                 <th>MCQ Marks</th>
                 <th>MCQ Pass Marks</th>
                 <th>Written Marks</th>
                 <th>Written Pass Marks</th>
               </tr>
                <?php 
                foreach($subjects as $subject){ ?>
                  <tr>
                        <td>
                          <input type="hidden" name="subject_ids[]" value="<?php echo $subject->id?>">
                          <input type="text"  value="<?php echo $subject->name?>" readonly>
                        </td>

                        <td>
                         <?php if ($subject->parent_id == 1){?>
                         <input type="text" name="mcq_mark[]" placeholder="MCQ marks" 
                          value="<?php echo $active_record[$subject->id]['mcq_mark'];?>">
                        <?php } else{?>
                          <input type="hidden" name="mcq_mark[]" value="0">
                          <input type="text" readonly value="N/A">
                        <?php }?>
                       </td>
                       <td>
                        <?php if ($subject->parent_id == 0){?>
                            <input type="text" name="mcq_pass_mark[]" placeholder="MCQ pass marks"
                         value="<?php echo $active_record[$subject->id]['mcq_pass_mark'];?>">
                        <?php }else{?>
                             <input type="hidden" name="mcq_pass_mark[]" value="0">
                            <input type="text" readonly value="N/A">
                        <?php } ?>
                       
                       </td>
                       <td>
                        <?php if ($subject->parent_id == 1){?>
                         <input type="text" name="written_mark[]" placeholder="Written marks"
                         value="<?php echo $active_record[$subject->id]['written_mark'];?>" >
                       <?php } else{?>
                        <input type="hidden" name="written_mark[]" value="0">
                        <input type="text" readonly value="N/A">
                      <?php } ?>
                       </td>
                       <td>
                        <?php if ($subject->parent_id == 0){?>
                            <input type="text" name="written_pass_mark[]" placeholder="Written pass marks"
                         value="<?php echo $active_record[$subject->id]['written_pass_mark'];?>">
                        <?php }else{?>
                             <input type="hidden" name="written_pass_mark[]" value="0">
                             <input type="text" readonly value="N/A">
                           
                        <?php } ?>
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