<?php

//print_r($subject); exit;
if(isset($_POST['name'])){
        global $wpdb;
        $table_name = $wpdb->prefix . 'subject';
        $data = $_POST;
        extract($data);
        $id = $_GET['id'];
        $mendatory = isset($mendatory) ? 1: 0;
        $sql = "UPDATE $table_name SET name='$name', group_name='$group_name', mendatory= $mendatory WHERE id=$id";
        //echo $sql; exit;
        $wpdb->query($wpdb->prepare($sql));
       
}

global $wpdb;
$sql = "SELECT * FROM ".$wpdb->prefix."subject WHERE id = ".$_GET['id'];
$subject = $wpdb->get_results($sql)[0];

?>
<div class="wrap">
  <h2 style="margin-bottom: 20px;">Update subject</h2> 
  <form method="post" name="add_subject_form">
        <table>
                <tr>
                        <td>Name</td>
                        <td><input type="text" name="name" value="<?php echo $subject->name; ?>" required></td>
                </tr>
                
                 <tr>
                        <td>Group</td>
                        <td>
                          <select name="group_name" style="width: 100%;" required>
                             <option value="all" <?php if ($subject->group_name ==  "all")  echo "selected" ?> >সকল বিভাগ</option> 
                             <option value="বিজ্ঞান" <?php if ($subject->group_name ==  "বিজ্ঞান")  echo "selected" ?> >বিজ্ঞান</option>   
                             <option value="মানবিক" <?php if ($subject->group_name ==  "মানবিক")  echo "selected" ?>>মানবিক</option>
                             <option value="ব্যাবসায় শিক্ষা" <?php if ($subject->group_name ==  "ব্যাবসায় শিক্ষা")  echo "selected" ?>>ব্যাবসায় শিক্ষা</option>
                          </select>
                        </td>
                </tr> 
                 <tr>
                        <td>Mendatory</td>
                        <td><input type="checkbox" name="mendatory" value="<?php echo $subject->mendatory; ?>" 
                                <?php if ($subject->mendatory == 1) echo 'checked'; ?> ></td>
                </tr>
                <tr>
                        <td colspan="2">
                                <button type="submit" class="button-primary" style="margin-top: 20px; float: right;"> Update</button>
                        </td>
                </tr>       
        </table>
        
  </form>
</div>