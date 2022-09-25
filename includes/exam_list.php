<?php
require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
class ExamConfigTable extends WP_List_Table{

	public function prepare_items(){

		$order_by = isset($_GET['order_by'])? trim($_GET['order_by']) : '';
		$order = isset($_GET['order'])? trim($_GET['order']) : '';
        $search_term = isset($_POST['s'])? trim($_POST['s']) : "";
        $data = $this->wp_list_data($order_by,$order,$search_term);
        //print_r($data); exit;
        $per_page = 5;
        $current_page = $this->get_pagenum();
        $total_items = count($data);

        $this->set_pagination_args(array(
               'total_items' => $total_items,
               'per_page' => $per_page
        ));

		$this->items = array_slice($data, (($current_page - 1)*$per_page), $per_page);
		$columns = $this->get_columns();
		$hidden = ['id'];
		$sortable = [
			'name' => ['name', false], // true means descending order, false means ascending order
			'subject' => ['subject', false], // true means descending order, false means ascending order
		];
		$this->_column_headers = array($columns, $hidden, $sortable);
	}

	public function wp_list_data($order_by = '', $order = '', $search_term = ''){
		// TODO : order_by, order, search_term will be applied to sql;
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix."exam as exam ORDER BY id DESC";
	
		if($search_term !=''){
			$sql .=" WHERE exam.name LIKE '%".$search_term."%'";
		}
		//$sql .=" ORDER BY student.name ASC";
		$raw_data = $wpdb->get_results($sql);
		$data = [];
		

		if(count($raw_data) > 0){
			foreach($raw_data as $single){
				$row['id'] = $single->id;
				$row['name'] = $single->name;
				$row['session_start'] = $single->session_start;
				$row['session_end'] = $single->session_end;
				$row['status'] = $single->status;
				$data[] = $row;
		  }
   }
      //echo '<pre>'; print_r($data); exit;
		return $data;
	}

	public function get_columns(){
          $columns = [
              "id" => "ID",
              "name" => "Name",
              "session_start" => "Session Start",
              "session_end" => "Session End",
              "status" => "Status",
          ];

          return $columns;
	}

	public function column_default($item, $column_name){
	//	print_r($item); exit;
		switch ($column_name) {
			case 'id':
			case 'name':
			case 'session_start':
			case 'session_end':
			case 'status':
			    return $item[$column_name];
			default:
				return 'No Value';
		}
	}

	public function column_name($item){
		$action = array(
            "edit" => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a', $_GET['page'],'edit',$item['id']),
            "config" => sprintf('<a href="?page=%s&action=%s&id=%s"> | Config</a', $_GET['page'],'config',$item['id']),
            "result_input" => sprintf('<a href="?page=%s&action=%s&id=%s"> | Input Marks</a', $_GET['page'],'result_input',$item['id']),
            
            "result_publish" => sprintf('<a href="?page=%s&action=%s&id=%s"> | Publish Result</a', $_GET['page'],'result_publish',$item['id']),
            "result_show" => sprintf('<a href="?page=%s&action=%s&id=%s"> | Show Result</a', $_GET['page'],'result_show',$item['id'])
            
		);

		return sprintf('%1$s %2$s', $item['name'], $this->row_actions($action));
	}
}

function show_exam_list() {?>
	<div class="wrap">
        <h2 style="margin-bottom: 20px;">Exam Configurations <a class="button-primary" href="<?php echo admin_url('admin.php?page=itscholarbd_exam_configuration&action=add'); ?>">Add</a></h2> 
<?php 
  $subject_table = new ExamConfigTable();
  $subject_table->prepare_items();
  echo  "<form method='post' name='frm_search_post' action='".$_SERVER['PHP_SELF']."?page=itscholarbd_student_subject'>";
     $subject_table->search_box("Search ", "student_subject_search_box_id");
  echo '</form>';
  $subject_table->display();
  echo '</div>';
}
?>