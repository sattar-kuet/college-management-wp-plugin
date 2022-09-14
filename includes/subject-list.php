<?php
require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
class SubjectTable extends WP_List_Table{

	public function prepare_items(){

		$order_by = isset($_GET['order_by'])? trim($_GET['order_by']) : '';
		$order = isset($_GET['order'])? trim($_GET['order']) : '';
        $search_term = isset($_POST['s'])? trim($_POST['s']) : "";
        $data = $this->wp_list_data($order_by,$order,$search_term);

        $per_page = 10;
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
			'name' => ['name', false] // true means descending order, false means ascending order
		];
		$this->_column_headers = array($columns, $hidden, $sortable);
	}

	public function wp_list_data($order_by = '', $order = '', $search_term = ''){
		// TODO : order_by, order, search_term will be applied to sql;
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->prefix."subject";
		if($search_term !=''){
			$sql .=" WHERE name LIKE '%".$search_term."%'";
		}
		$sql .=" ORDER BY name ASC";
		$raw_data = $wpdb->get_results($sql);
		$data = [];
		if(count($raw_data) > 0){
			foreach($raw_data as $single){
				$row['id'] = $single->id; 
				$row['name'] = $single->name; 
				$row['group_name'] = $single->group_name; 
				$row['mendatory'] = $single->mendatory == 0? 'NO': 'YES'; 
				$data[] = $row;
			}
		}


		return $data;
	}

	public function get_columns(){
          $columns = [
              "id" => "ID",
              "name" => "Name",
              "group_name" => "Group",
              "mendatory" => "Mendatory"
          ];

          return $columns;
	}

	public function column_default($item, $column_name){
		switch ($column_name) {
			case 'id':
			case 'name':
			case 'mendatory':
			case 'group_name':
			    return $item[$column_name];
			default:
				return 'No Value';
		}
	}

	public function column_name($item){
		$action = array(
            "edit" => sprintf('<a href="?page=%s&action=%s&id=%s">Edit</a', $_GET['page'],'edit',$item['id']),
            "delete" => sprintf('<a href="?page=%s&action=%s&id=%s">  Delete</a', $_GET['page'],'delete',$item['id']),
		);

		return sprintf('%1$s %2$s', $item['name'], $this->row_actions($action));
	}
}

function show_subject_list() {?>
	<div class="wrap">
        <h2 style="margin-bottom: 20px;">Subjects <a class="button-primary" href="<?php echo admin_url('admin.php?page=itscholarbd_subject&action=add'); ?>">Add</a></h2> 
<?php 
  $subject_table = new SubjectTable();
  $subject_table->prepare_items();
  echo  "<form method='post' name='frm_search_post' action='".$_SERVER['PHP_SELF']."?page=itscholarbd_subject'>";
     $subject_table->search_box("Search Subject", "subject_search_box_id");
  echo '</form>';
  $subject_table->display();
}
?>
</div>