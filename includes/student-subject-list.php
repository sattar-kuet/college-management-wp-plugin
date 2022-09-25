<?php
require_once(ABSPATH.'wp-admin/includes/class-wp-list-table.php');
class StudentSubjectTable extends WP_List_Table{

	public function prepare_items(){

		$order_by = isset($_GET['order_by'])? trim($_GET['order_by']) : '';
		$order = isset($_GET['order'])? trim($_GET['order']) : '';
        $search_term = isset($_POST['s'])? trim($_POST['s']) : "";
        $data = $this->wp_list_data($order_by,$order,$search_term);

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
		$sql = "SELECT student.name as studnet_name,student.id as studnet_id,student.group_name as studnet_group,student.roll as student_roll,subject.name as subject_name,student_subject.subject_type as subject_type FROM ".$wpdb->prefix."student as student";
		$sql .= " LEFT JOIN ".$wpdb->prefix."student_subject as student_subject ON student.id =  student_subject.student_id";
		$sql .= " LEFT JOIN ".$wpdb->prefix."subject as subject ON subject.id =  student_subject.subject_id";
	
		if($search_term !=''){
			$sql .=" WHERE student.name LIKE '%".$search_term."%'";
			$sql .=" OR student.roll = ".$search_term;
		}
		//$sql .=" ORDER BY student.name ASC";
		$raw_data = $wpdb->get_results($sql);
		$data = [];
		
		$readable_subject_type[0] = 'Optional';
		$readable_subject_type[1] = 'Compulsory';
		$student_enlisted = [];
		$index = -1;
		if(count($raw_data) > 0){
			foreach($raw_data as $single){
        
        if(!isset($student_enlisted[$single->studnet_id])){
        	$index++;
        }
      	$data[$index]['student'] = [
             'id' => $single->studnet_id,
             'name' => $single->studnet_name,
             'roll' => $single->student_roll,
             'group_name' => $single->studnet_group,
      	]; 
      	if($single->subject_type != -1 && !empty($single->subject_name)){
      		$data[$index]['subject'][] = [
		         'name' => $single->subject_name,
		         'subject_type' => $readable_subject_type[$single->subject_type],
			    ];
      	}
        $student_enlisted[$single->studnet_id] = 1;
			}
		}
   //echo '<pre>'; print_r($data); exit;
   $final_data = [];
   foreach($data as $row){
      $final_row['id'] = $row['student']['id'];
      $final_row['name'] = $row['student']['name'];
      $final_row['roll'] = $row['student']['roll'];
      $final_row['group_name'] = $row['student']['group_name'];
      $subject_list = 'Not Configured';
      if(isset($row['subject'])){
      	$subject_list = '<ul>';
      	foreach($row['subject'] as $subject){
	      	if(isset($subject['name']) && isset($subject['subject_type'])){
	      		$subject_list .='<li>'.$subject['name'].' ['.$subject['subject_type'].']</li>';
	      	} 
	      }
        $subject_list .= '<ul>';
      }
      
      
      $final_row['subject'] = $subject_list;
      $final_data[] = $final_row;
   }
      //echo '<pre>'; print_r($final_data); exit;
		return $final_data;
	}

	public function get_columns(){
          $columns = [
              "id" => "ID",
              "roll" => "Roll",
              "name" => "Name",
              "group_name" => "Group",
              "subject" => "Subject"
          ];

          return $columns;
	}

	public function column_default($item, $column_name){
		switch ($column_name) {
			case 'id':
			case 'name':
			case 'roll':
			case 'group_name':
			case 'subject':
			    return $item[$column_name];
			default:
				return 'No Value';
		}
	}

	public function column_name($item){
		$action = array(
            "config" => sprintf('<a href="?page=%s&action=%s&id=%s">Config</a', $_GET['page'],'config',$item['id']),
            "delete" => sprintf('<a href="?page=%s&action=%s&id=%s"> | Delete</a', $_GET['page'],'delete',$item['id']),
		);
		return sprintf('%1$s %2$s', $item['name'], $this->row_actions($action));
	}
}

function show_student_subject_list() {?>
	<div class="wrap">
        <h2 style="margin-bottom: 20px;">Student Subjects</h2> 
<?php 
  $subject_table = new StudentSubjectTable();
  $subject_table->prepare_items();
  echo  "<form method='post' name='frm_search_post' action='".$_SERVER['PHP_SELF']."?page=itscholarbd_student_subject'>";
     $subject_table->search_box("Search ", "student_subject_search_box_id");
  echo '</form>';
  $subject_table->display();
  echo '</div>';
}
?>