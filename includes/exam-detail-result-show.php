<?php
require plugin_dir_path( __FILE__ ) . 'utility.php';
require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');

unset($GROUPS[$ALL]);
global $wpdb;

class ProcessedDetailResultListTable extends WP_List_Table{

  public function prepare_items(){

    $order_by = isset($_GET['order_by'])? trim($_GET['order_by']) : '';
    $order = isset($_GET['order'])? trim($_GET['order']) : '';
        $search_term = isset($_POST['s'])? trim($_POST['s']) : "";
        $data = $this->wp_list_data($order_by,$order,$search_term);
        //print_r($data); exit;
        $per_page = 20;
        $current_page = $this->get_pagenum();
        $total_items = count($data);

        $this->set_pagination_args(array(
               'total_items' => $total_items,
               'per_page' => $per_page
        ));

    $this->items = array_slice($data, (($current_page - 1)*$per_page), $per_page);
    $columns = $this->get_columns();
    $hidden = ['id','student_id'];
    $sortable = [
      'subject_name' => ['subject_name', false], // true means descending order, false means ascending order
    ];
    $this->_column_headers = array($columns, $hidden, $sortable);
  }

  public function wp_list_data($order_by = '', $order = '', $search_term = ''){
    // TODO : order_by, order, search_term will be applied to sql;
    global $wpdb;
    $sql = "SELECT  processed_result.id as processed_result_id, subject.name as subject_name, student.id as student_id, student.name as student_name, student.roll as student_roll, student.group_name as student_group, processed_result.total_mark as total_mark, processed_result.gpa as gpa, processed_result.grade_name as grade_name,processed_result.group_name as group_name,processed_result.exam_id as exam_id  FROM ".$wpdb->prefix."processed_result as processed_result";
    $sql .= " LEFT JOIN ".$wpdb->prefix."student as student ON processed_result.student_id = student.id";
    $sql .= " LEFT JOIN ".$wpdb->prefix."subject as subject ON processed_result.subject_id = subject.id";
    $sql .=" WHERE processed_result.subject_id !=0 ";
    $sql .=" AND processed_result.student_id=".$_GET['id'];
    $sql .=" ORDER BY processed_result.gpa DESC";
  
    if($search_term !=''){
      $sql .=" WHERE exam.name LIKE '%".$search_term."%'";
    }
    //echo $sql;
    $raw_data = $wpdb->get_results($sql);
   //echo '<pre>'; print_r($raw_data); exit;
   
    $raw_data_array = [];
    if(count($raw_data) > 0){
      foreach($raw_data as $single){
        $row['id'] = $single->processed_result_id;
        $row['student_id'] = $single->student_id;
        $row['name'] = $single->student_name;
        $row['subject_name'] = $single->subject_name;
        $row['total_mark'] = $single->total_mark;
        $row['gpa'] = $single->gpa;
        $row['grade_name'] = $single->grade_name;
        
        $raw_data_array[] = $row;
      }
    }
  //echo '<pre>'; print_r($raw_data_array);
    //usort($raw_data_array, "cmp");
   
    array_multisort(array_column($raw_data_array, 'total_mark'), SORT_DESC, $raw_data_array);
    
   //   print_r($raw_data_array);
   
   
    $data = [];
    $merit_position = 1;

    if(count($raw_data_array) > 0){
      foreach($raw_data_array as $single){
        $row['id'] = $single['id'];
        $row['student_id'] = $single['student_id'];
        $row['name'] = $single['name'];
        $row['subject_name'] = $single['subject_name'];
        $row['total_mark'] = $single['total_mark'];
        $row['gpa'] = round($single['gpa'],2);
        $row['grade_name'] = $single['grade_name'];
        
        $data[] = $row;
      }
   }
     // echo '<pre>'; print_r($data); exit;
    return $data;
  }
function cmp($a, $b) {
  return $a['total_mark'] < $b['total_mark'];
}
  public function get_columns(){
          $columns = [
              "id" => "ID",
              "student_id" => "Student ID",
              "subject_name" => "Subject name",
              "gpa" => "GPA",
              "grade_name" => "Grade",
              "total_mark" => "Total Mark"
          ];

          return $columns;
  }

  public function column_default($item, $column_name){
  //  print_r($item); exit;
    switch ($column_name) {
      case 'id':
      case 'student_id':
      case 'subject_name':
      case 'gpa':
      case 'grade_name':
      case 'total_mark':
      case 'merit_position':
          return $item[$column_name];
      default:
        return 'No Value';
    }
  }

  public function column_name($item){
    //print_r($item);
    $action = [];
    if($_GET['action'] != 'edit'){
      $action["edit"] = sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a', $_GET['page'],'edit',$item['id']);
    }
    if($_GET['action'] != 'config'){
      $action["config"] = sprintf('<a href="?page=%s&action=%s&id=%s"> | Config</a', $_GET['page'],'config',$item['id']);
    }
    if($_GET['action'] != 'result_input'){
      $action["result_input"] = sprintf('<a href="?page=%s&action=%s&id=%s"> | Result Input</a', $_GET['page'],'result_input',$item['id']);
    }
    if($_GET['action'] != 'result_publish'){
      $action["result_publish"] = sprintf('<a href="?page=%s&action=%s&id=%s"> | Result Publish</a', $_GET['page'],'result_publish',$item['id']);
    }
    
    if($_GET['action'] != 'result_show'){
      $action["result_show"] = sprintf('<a href="?page=%s&action=%s&id=%s"> | Result Show</a', $_GET['page'],'result_show',$item['id']);
    }
    if($_GET['action'] != 'detail_result_show'){
      $action["detail_result_show"] = sprintf('<a href="?page=%s&action=%s&id=%s"> | Detail Result Show</a', $_GET['page'],'detail_result_show',$item['id']);
    }

    return sprintf('%1$s %2$s', $item['subject_name'], $this->row_actions($action));
  }
}

function show_detail_result_list() {
  $subject_table = new ProcessedDetailResultListTable();
  $subject_table->prepare_items();
  echo  "<form method='post' name='frm_search_post' action='".$_SERVER['PHP_SELF']."?page=itscholarbd_student_subject'>";
     $subject_table->search_box("Search ", "student_subject_search_box_id");
  echo '</form>';
  $subject_table->display();
}

$sql = "SELECT * FROM ".$wpdb->prefix."student";
$sql .=" WHERE id =".$_GET['id'];
//echo $sql; exit;
$student = $wpdb->get_results($sql); 
$student = $student[0];

?>
<div class="wrap">
   
     <h2 style="margin-bottom: 20px;">Result of <?php echo $student->name; ?> </h2> 
    
  <?php
    show_detail_result_list();
  ?>
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