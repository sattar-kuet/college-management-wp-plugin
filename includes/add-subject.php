<?php
 require plugin_dir_path( __FILE__ ) . 'utility.php';
if(isset($_POST['name'])){
        //echo 'here'; exit;

        global $wpdb;
        $table = $wpdb->prefix.'subject';
        $data = $_POST;
        $formatted_data = [];
        $formatted_data['name'] = $data['name'];
        $formatted_data['group_name'] = $data['group_name'];
        if(isset($data['mendatory'])){
           $formatted_data['mendatory'] = 1;
        }else{
          $formatted_data['mendatory'] = 0;
        }
        if(isset($data['has_two_part'])){
          $formatted_data['has_two_part'] = 1;
        }else{
          $formatted_data['has_two_part'] = 0;
        }
        $formatted_data['parent_id'] = 0;
       // echo '<pre>'; print_r($formatted_data); exit;
        $wpdb->insert($table,$formatted_data);
        $parent_id = $wpdb->insert_id;
        
        if(isset($data['has_two_part'])){
          $child_subject['name'] = $data['first_part_name'];
          $child_subject['parent_id'] = $parent_id;
          $child_subject['has_two_part'] = 0;
          $child_subject['group_name'] = $data['group_name'];
          $child_subject['mendatory'] = $formatted_data['mendatory'];
          $wpdb->insert($table,$child_subject);
          $child_subject['name'] = $data['second_part_name'];
          $wpdb->insert($table,$child_subject);
      }

      //  print_r($result); exit;
}
?>
<div class="wrap">
  <h2 style="margin-bottom: 20px;">Add new subject</h2> 
  <form method="post" name="add_student_form">
        <table>
                <tr>
                        <td>Name</td>
                        <td><input type="text" name="name" class="subject_name" required></td>
                        <td>Has Two Part</td>
                        <td><input type="checkbox" name="has_two_part" id="has_two_part"></td>
                </tr>
                <tr id="first_part">
                   <td>First Paper Name</td>
                   <td><input type="text" name="first_part_name" class="first_part_name"></td>
                </tr>
                <tr id="second_part">
                   <td>Second Paper Name</td>
                   <td><input type="text" name="second_part_name" class="second_part_name"></td>
                </tr>
                
                 <tr>
                        <td>Group</td>
                        <td colspan="2">
                          <select name="group_name" style="width: 100%;" required>
                            <?php foreach($GROUPS as $group) {?>
                             <option value="<?php echo $group;?>"><?php echo $group;?></option>
                           <?php } ?>
                          </select>
                        </td>
                </tr> 
                <tr>
                        <td>Mendatory</td>
                        <td colspan="2"><input type="checkbox" name="mendatory" value="1" checked ></td>
                </tr>
                <tr>
                        <td colspan="4">
                                <button type="submit" class="button-primary" style="margin-top: 20px; float: right;"> Submit</button>
                        </td>
                </tr>       
        </table>
        
  </form>
</div>