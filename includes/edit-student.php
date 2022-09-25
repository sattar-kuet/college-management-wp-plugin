<?php
require plugin_dir_path( __FILE__ ) . 'utility.php';
unset($GROUPS[$ALL]);
if(isset($_POST['name'])){
        global $wpdb;
        $table_name = $wpdb->prefix . 'student';
        $data = $_POST;
        extract($data);
        $id = $_GET['id'];
        $sql = "UPDATE $table_name SET name='$name', roll=$roll, group_name='$group_name', session_start=$session_start,session_end=$session_end WHERE id=$id";
        //echo $sql; exit;
        $wpdb->query($wpdb->prepare($sql));
       
}

global $wpdb;
$sql = "SELECT * FROM ".$wpdb->prefix."student WHERE id = ".$_GET['id'];
$student = $wpdb->get_results($sql)[0];

?>
<div class="wrap">
  <h2 style="margin-bottom: 20px;">Update student</h2> 
  <form method="post" name="add_student_form">
        <table>
                <tr>
                        <td>Name</td>
                        <td><input type="text" name="name" value="<?php echo $student->name; ?>" required></td>
                </tr>
                <tr>
                        <td>Roll</td>
                        <td><input type="text" name="roll" value="<?php echo $student->roll; ?>" required></td>
                </tr>
                 <tr>
                        <td>Group</td>
                        <td>
                          <select name="group_name" style="width: 100%;" required>
                            <?php foreach($GROUPS as $group) {?>
                             <option value="<?php echo $group;?>" <?php if ($student->group_name ==  $group)  echo "selected" ?> ><?php echo $group;?></option>
                           <?php } ?>
                          </select>
                        </td>
                </tr> 
                 <tr>
                        <td>Session Start</td>
                        <td><input type="number" name="session_start" class="session_start"  value="<?php echo $student->session_start; ?>" required></td>
                </tr>
                <tr>
                        <td>Session End</td>
                        <td><input type="number" name="session_end" class="session_end" value="<?php  echo $student->session_end; ?>" required></td>
                </tr>
                <tr>
                        <td colspan="2">
                                <button type="submit" class="button-primary" style="margin-top: 20px; float: right;"> Submit</button>
                        </td>
                </tr>       
        </table>
        
  </form>
</div>