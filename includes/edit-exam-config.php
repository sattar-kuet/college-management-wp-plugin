<?php


if(isset($_POST['name'])){
        global $wpdb;
        $table_name = $wpdb->prefix . 'exam';
        $data = $_POST;
        extract($data);
        $id = $_GET['id'];
        $sql = "UPDATE $table_name SET name='$name', session_start='$session_start', session_end='$session_end' WHERE id=$id";
        //echo $sql; exit;
        $wpdb->query($wpdb->prepare($sql));
       
}

global $wpdb;
$sql = "SELECT * FROM ".$wpdb->prefix."exam WHERE id = ".$_GET['id'];
$exam = $wpdb->get_results($sql)[0];
//print_r($exam);

?>
<div class="wrap">
  <h2 style="margin-bottom: 20px;">Edit exam setup</h2> 
  <form method="post" name="add_student_form">
        <table>
                <tr>
                        <td>Name</td>
                        <td><input type="text" name="name" value="<?php echo $exam->name; ?>" required></td>
                </tr>
                <tr>
                        <td>Session Start</td>
                        <td><input type="text" name="session_start" value="<?php echo $exam->session_start; ?>" required></td>
                </tr>
                 <tr>
                        <td>Session End</td>
                        <td><input type="text" name="session_end" value="<?php echo $exam->session_end; ?>" required></td>
                </tr>
                 
                <tr>
                        <td colspan="2">
                                <button type="submit" class="button-primary" style="margin-top: 20px; float: right;"> Submit</button>
                        </td>
                </tr>       
        </table>
        
  </form>
</div>