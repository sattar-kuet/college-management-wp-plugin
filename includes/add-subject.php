<?php
if(isset($_POST['name'])){
        //echo 'here'; exit;

        global $wpdb;
        $table = $wpdb->prefix.'subject';
        $data = $_POST;
        if(!isset($data['mendatory'])){
           $data['mendatory'] = 0;
        }
        $result = $wpdb->insert($table,$data);
        //print_r($result); exit;
}
?>
<div class="wrap">
  <h2 style="margin-bottom: 20px;">Add new subject</h2> 
  <form method="post" name="add_student_form">
        <table>
                <tr>
                        <td>Name</td>
                        <td><input type="text" name="name" required></td>
                </tr>
                
                 <tr>
                        <td>Group</td>
                        <td>
                          <select name="group_name" style="width: 100%;" required>
                             <option value="all">সকল বিভাগ</option>   
                             <option value="বিজ্ঞান">বিজ্ঞান</option>   
                             <option value="মানবিক">মানবিক</option>
                             <option value="ব্যাবসায় শিক্ষা">ব্যাবসায় শিক্ষা</option>
                          </select>
                        </td>
                </tr> 
                <tr>
                        <td>Mendatory</td>
                        <td><input type="checkbox" name="mendatory" value="1" checked ></td>
                </tr>
                <tr>
                        <td colspan="2">
                                <button type="submit" class="button-primary" style="margin-top: 20px; float: right;"> Submit</button>
                        </td>
                </tr>       
        </table>
        
  </form>
</div>