<?php
if(isset($_POST['name'])){
        //echo 'here'; exit;
        global $wpdb;
        $table = $wpdb->prefix.'exam';
        $data = $_POST;
        //echo $table; exit;
        $result = $wpdb->insert($table,$data);
        //print_r($result); exit;
}
?>
<div class="wrap">
  <h2 style="margin-bottom: 20px;">Add new exam setup</h2> 
  <form method="post" name="add_student_form">
        <table>
                <tr>
                        <td>Name</td>
                        <td><input type="text" name="name" required></td>
                </tr>
                <tr>
                        <td>Session Start</td>
                        <td><input type="text" name="session_start" required></td>
                </tr>
                 <tr>
                        <td>Session End</td>
                        <td><input type="text" name="session_end" required></td>
                </tr>
                 
                <tr>
                        <td colspan="2">
                                <button type="submit" class="button-primary" style="margin-top: 20px; float: right;"> Submit</button>
                        </td>
                </tr>       
        </table>
  </form>
</div>