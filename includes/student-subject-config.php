<?php
require plugin_dir_path( __FILE__ ) . 'utility.php';

global $wpdb;
$sql = "SELECT * FROM ".$wpdb->prefix."student WHERE id=".$_GET['id'];
$student = $wpdb->get_results($sql)[0];

$sql = "SELECT * FROM ".$wpdb->prefix."subject WHERE group_name='".$student->group_name."'";
$sql .= " OR group_name='$ALL'";
$subjects = $wpdb->get_results($sql);

//print_r($subject_type); exit;

if(isset($_POST['submit_btn'])){

  $subject_ids = $_POST['subject_ids'];
  $mendatory = $_POST['mendatory'];
  //echo '<pre>'; print_r($_POST); exit;
  global $wpdb;
  $table = $wpdb->prefix.'student_subject';

  for($i=0; $i < count($subject_ids); $i++){
        $sql = "SELECT * FROM ".$table." WHERE student_id=".$_GET['id'];
        $sql .= " AND subject_id=".$subject_ids[$i];
        $old_record = $wpdb->get_results($sql);
        if(count($old_record)){
          $id = $old_record[0]->id;
          $sql = "UPDATE $table SET subject_type=$mendatory[$i] WHERE id=$id";
          //echo $sql; exit;
          $wpdb->query($wpdb->prepare($sql));
        }
        else{
          $data['student_id'] = $_GET['id'];
          $data['subject_id'] = $subject_ids[$i];
          $data['subject_type'] = $mendatory[$i];
          $result = $wpdb->insert($table,$data);
        }
        
  }
}
$sql = "SELECT * FROM ".$wpdb->prefix."student_subject WHERE student_id='".$student->id."'";
$student_subjects = $wpdb->get_results($sql);
$subject_type = [];
foreach($student_subjects as $student_subject){
  $subject_type[$student_subject->subject_id] = $student_subject->subject_type;
} 

//echo '<pre>'; print_r($subject_type);

?>
<div class="wrap">
  <h2 style="margin-bottom: 20px;">Subject configuration for </h2> 
  <h3><?php echo $student->roll.'  '. $student->name. ' '.$student->group_name;  ?></h3>
  <form method="post" name="add_student_form">
        <table>
                <?php 
                foreach($subjects as $subject){ ?>
                  <tr>
                        <td>
                          <input type="hidden" name="subject_ids[]" value="<?php echo $subject->id?>">
                          <input type="text"  value="<?php echo $subject->name?>" readonly>
                        </td>

                        <td>
                          <select name="mendatory[]" class="subject_options">
                            <?php if ($subject->mendatory == 0){ ?>
                            <option value="-1" <?php if (count($subject_type)>0 && $subject_type[$subject->id] == -1) echo 'selected'; ?> >X</option>
                            <option value="0" <?php if (count($subject_type)>0 && $subject_type[$subject->id] == 0) echo 'selected';?>  <?php  echo 'class="optional_subject optional_subject'.$subject->id.'"'; echo 'data-subjectid="'.$subject->id.'"'; ?> >Optional</option>
                          <?php } ?>
                            <option value="1" <?php if (count($subject_type)>0 && $subject_type[$subject->id] == 1) echo 'selected'; ?> <?php echo 'class="mendatory_subject mendatory_subject'.$subject->id.'"'; echo 'data-subjectid="'.$subject->id.'"'; ?> >Compolsury</option>
                          </select>
                          
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