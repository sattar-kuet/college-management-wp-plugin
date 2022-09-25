<?php 
function exam_config($exam_id){
	global $wpdb;
	$sql = "SELECT exam_config.mcq_mark as mcq_mark, exam_config.mcq_pass_mark as mcq_pass_mark,exam_config.written_mark as written_mark, exam_config.written_pass_mark as written_pass_mark, subject.id as subject_id, subject.parent_id as subject_parent FROM ".$wpdb->prefix."exam_configuration exam_config";
  $sql .= " LEFT JOIN wp_subject as subject ON exam_config.subject_id = subject.id";
  $sql .=" WHERE exam_config.exam_id=".$exam_id;
	$exam_configurations = $wpdb->get_results($sql);
	//echo $sql; exit;
  //echo '<pre>'; print_r($exam_configurations); exit;
	$active_record = [];
	foreach($exam_configurations as $exam_configuration){
    $subject_id = ($exam_configuration->subject_parent != 0)? $exam_configuration->subject_parent : $exam_configuration->subject_id;
    if(isset($active_record[$subject_id]['mcq_mark'])){
      $active_record[$subject_id]['mcq_mark'] += $exam_configuration->mcq_mark;
    }else{
      $active_record[$subject_id]['mcq_mark'] = $exam_configuration->mcq_mark;
    }

    if(isset($active_record[$subject_id]['mcq_pass_mark'])){
      $active_record[$subject_id]['mcq_pass_mark'] += $exam_configuration->mcq_pass_mark;
    }else{
      $active_record[$subject_id]['mcq_pass_mark'] = $exam_configuration->mcq_pass_mark;
    }

    if(isset($active_record[$subject_id]['written_mark'])){
      $active_record[$subject_id]['written_mark'] += $exam_configuration->written_mark;
    }else{
      $active_record[$subject_id]['written_mark'] = $exam_configuration->written_mark;
    }

    if(isset($active_record[$subject_id]['written_pass_mark'])){
      $active_record[$subject_id]['written_pass_mark'] += $exam_configuration->written_pass_mark;
    }else{
      $active_record[$subject_id]['written_pass_mark'] = $exam_configuration->written_pass_mark;
    }
	}
  //echo '<pre>'; print_r($active_record); exit;
	return $active_record;
}

function process_result($exam_id = 0){
	global $wpdb;
	$sql = "SELECT * FROM ".$wpdb->prefix."student_result WHERE exam_id=".$exam_id." ORDER BY student_id ASC";
    $exam_results = $wpdb->get_results($sql); 
    $total = [];
    $failed_student_list = [];
    $exam_config = exam_config($exam_id);
    //echo '<pre>'; print_r($exam_results); 
    $pre_formatted_data = [];
    foreach ($exam_results as $exam_result) {

      $subject_id = ($exam_result->subject_parent_id != 0) ? 
                    $exam_result->subject_parent_id : $exam_result->subject_id;
      $student_id = $exam_result->student_id;
      $pre_formatted_data[$student_id][$subject_id]['exam_id']  = $exam_id;
      $pre_formatted_data[$student_id][$subject_id]['group_name']  =  $exam_result->group_name;
      $pre_formatted_data[$student_id][$subject_id]['subject_id']  =  $subject_id;
      $pre_formatted_data[$student_id][$subject_id]['subject_type']  =  $exam_result->subject_type;

      if(isset($pre_formatted_data[$student_id][$subject_id]['mcq_mark'])){
           $pre_formatted_data[$student_id][$subject_id]['mcq_mark']  +=  $exam_result->mcq_mark;
      }else{
           $pre_formatted_data[$student_id][$subject_id]['mcq_mark']  =  $exam_result->mcq_mark;
      }
      if(isset($pre_formatted_data[$student_id][$subject_id]['written_mark'] )){
          $pre_formatted_data[$student_id][$subject_id]['written_mark']  +=  $exam_result->written_mark;
      }else{
        $pre_formatted_data[$student_id][$subject_id]['written_mark']  =  $exam_result->written_mark;
      }
      
    }
     
     //print_r($pre_formatted_data); exit;

    foreach($pre_formatted_data as $student_id => $pre_formatted_data_row){
       $exam_result = array_values($pre_formatted_data_row)[0];
       
       //echo '<pre>'; 
       //print_r($pre_formatted_data_row); 
      // print_r($exam_result); 
       //exit;
       $subject_id = $exam_result['subject_id'];

       $processed_result['exam_id'] = $exam_id;   
       $processed_result['group_name'] = $exam_result['group_name'];   
       $processed_result['student_id'] = $student_id;   
       $processed_result['subject_id'] = $subject_id;   
       $processed_result['mcq_mark'] = $exam_result['mcq_mark'];   
       $processed_result['written_mark'] = $exam_result['written_mark'];   
       $processed_result['total_mark'] = $exam_result['mcq_mark'] + $exam_result['written_mark'];  
       $maximum_mark = $exam_config[$subject_id]['mcq_mark'] + $exam_config[$subject_id]['written_mark'];
       $processed_result['percentage_mark'] = ($processed_result['total_mark']/$maximum_mark)*100;   
       $gpa_grade = process_gpa_and_grade($exam_config, $exam_result, $processed_result['percentage_mark']); 
       $processed_result['gpa'] = $gpa_grade['gpa'];     
       $processed_result['grade_name'] = $gpa_grade['grade']; 
       //print_r($processed_result); exit;
       add_or_update_subject_wise_result($processed_result);

       
       if(isset($total[$student_id]['mcq_mark'])){
       	  $total[$student_id]['mcq_mark'] +=  $exam_result['mcq_mark'];
       }else{
       	  $total[$student_id]['mcq_mark'] =  $exam_result['mcq_mark'];
       }

       if(isset($total[$student_id]['written_mark'])){
       	  $total[$student_id]['written_mark'] +=  $exam_result['written_mark'];
       }else{
       	  $total[$student_id]['written_mark'] =  $exam_result['written_mark'];
       } 

       if(isset($total[$student_id]['total_mark'])){
       	  $total[$student_id]['total_mark'] +=  $exam_result['mcq_mark'] + $exam_result['written_mark'];
       }else{
       	  $total[$student_id]['total_mark'] =  $exam_result['mcq_mark'] + $exam_result['written_mark'];
       }
       if(isset($total[$student_id]['total_gpa'])){
       	  if($exam_result['subject_type'] == 0 && $gpa_grade['gpa']>2){
       	  	$total[$student_id]['total_gpa'] +=  $gpa_grade['gpa'] - 2;
       	  }else{
       	  	 $total[$student_id]['total_gpa'] +=  $gpa_grade['gpa'];
       	  }
       	 
       }else{
       	  $total[$student_id]['total_gpa'] =  $gpa_grade['gpa'];
       }

      // echo $exam_result->subject_type.'<br/>';

       if(isset($total[$student_id]['total_subject'])){
        if($exam_result['subject_type'] == 1){

       	  $total[$student_id]['total_subject'] += 1;
        }
       }else{
       	  $total[$student_id]['total_subject'] =  1;
       }
       if($gpa_grade['gpa'] == 0){
       	$failed_student_list[$student_id] = 1;
       }

       if(isset($failed_student_list[$student_id])){
       	$total[$student_id]['total_gpa'] = 0;
       }
       $total[$student_id]['group_name'] = $exam_result['group_name'];
    }
    //echo '<pre>'; print_r($total); exit;
    add_or_update_overall_result($exam_id,$total);

}


function add_or_update_subject_wise_result($processed_result){
 // print_r($processed_result); exit;
	 global $wpdb;
	 $table = $wpdb->prefix."processed_result";
	 $sql = "SELECT * FROM ".$table." WHERE exam_id=".$processed_result['exam_id'];
        $sql .= " AND student_id=".$processed_result['student_id'];
        $sql .= " AND subject_id=".$processed_result['subject_id'];
       // echo $sql; exit;
        $old_record = $wpdb->get_results($sql);
        if(count($old_record)){
          $id = $old_record[0]->id;
          
          $sql = "UPDATE $table SET ";
          $sql .=" mcq_mark=".$processed_result['mcq_mark'];
          $sql .=", written_mark=".$processed_result['written_mark'];
          $sql .=", total_mark=".$processed_result['total_mark'];
          $sql .=", percentage_mark=".$processed_result['percentage_mark'];
          $sql .=", gpa=".$processed_result['gpa'];
          $sql .=", grade_name='".$processed_result['grade_name']."'";
          $sql .=" WHERE id=".$id;
          //echo $sql; exit;
          $wpdb->query($wpdb->prepare($sql));
        }else{
        	$wpdb->insert($table,$processed_result);
        }
}

function add_or_update_overall_result($exam_id,$total_list){
    //echo '<pre>';print_r($total_list); exit;
	 global $wpdb;
	 $table = $wpdb->prefix."processed_result";
	 foreach($total_list as $student_id => $total){
	 	$sql = "SELECT * FROM ".$table." WHERE exam_id=".$exam_id;
        $sql .= " AND student_id=".$student_id;
        $sql .= " AND subject_id=0";
        $old_record = $wpdb->get_results($sql);
        $total_subject = $total['total_subject'];
       // print_r($total_subject); exit;
        if($total_subject == 0){
            echo 'omg. something fishi'; exit;
          continue;
        }
        //print_r($total); exit;
        $gpa = $total['total_gpa']/$total_subject;
        
        if($gpa > 5){
          $gpa = 5;
        }
        $grade_name = grade_name_from_gpa($gpa);
        if(count($old_record)){
          $id = $old_record[0]->id;
          
          $sql = "UPDATE $table SET ";
          $sql .=" mcq_mark=".$total['mcq_mark'];
          $sql .=", written_mark=".$total['written_mark'];
          $sql .=", total_mark=".$total['total_mark'];
          $sql .=", gpa=".$gpa;
          $sql .=", grade_name='".$grade_name."'";
          $sql .=" WHERE id=".$id;
          //echo $sql; exit;
          $wpdb->query($wpdb->prepare($sql));
        }else{
          //echo '<pre>'; print_r($total);
          $processed_result['exam_id'] = $exam_id;
          $processed_result['group_name'] = $total['group_name'];
          $processed_result['student_id'] = $student_id;
          $processed_result['subject_id'] = 0;
          $processed_result['mcq_mark'] = $total['mcq_mark'];
          $processed_result['written_mark'] = $total['written_mark'];
          $processed_result['total_mark'] = $total['total_mark'];
          $processed_result['gpa'] = round($gpa, 2);
          $processed_result['grade_name'] = $grade_name;
          $processed_result['percentage_mark'] = 0;
          
          extract($processed_result);
          
        //   $sql = "INSERT INTO wp_processed_result (exam_id, group_name, student_id, subject_id, mcq_mark, written_mark,total_mark,gpa,grade_name,percentage_mark) VALUES ($exam_id, '$group_name', $student_id, 0, $mcq_mark, $gpa,'$grade_name',0)";
        //   echo $sql .'<br/>'; 
         $wpdb->insert($table,$processed_result);
         // echo $result.'<br/>';
        }
	 }
	 
}

function process_gpa_and_grade($exam_config, $exam_result, $percentage_mark){

	$mcq_pass_mark = $exam_config[$exam_result->subject_id]['mcq_pass_mark'];
	$written_pass_mark = $exam_config[$exam_result->subject_id]['written_pass_mark'];
//   echo '<pre>'; print_r($mcq_pass_mark);
//     print_r($written_pass_mark);
//     print_r($exam_result);
//     print_r($percentage_mark);
//      exit;
    if($exam_result->mcq_mark < $mcq_pass_mark || $exam_result->written_mark < $written_pass_mark){
     // echo 'wrong place1'; exit;
    	return ['gpa' => 0, 'grade' => 'Fail'];
    }
    if($percentage_mark>=80){
     // echo 'should be here'; exit;
    	return ['gpa' => 5.00, 'grade' => 'A+'];
    }
    else if($percentage_mark >=70 && $percentage_mark<80 ){
    	return ['gpa' => 4.00, 'grade' => 'A'];
    }
    else if($percentage_mark >=60 && $percentage_mark<70 ){
    	return ['gpa' => 3.50, 'grade' => 'A-'];
    } 
    else if($percentage_mark >=50 && $percentage_mark<60 ){
    	return ['gpa' => 3.00, 'grade' => 'B'];
    } 
    else if($percentage_mark >=40 && $percentage_mark<50 ){
    	return ['gpa' => 2.00, 'grade' => 'C'];
    } 
    else if($percentage_mark >=33 && $percentage_mark<40 ){
    	return ['gpa' => 1.00, 'grade' => 'D'];
    } else {
      //echo 'wrong place 2'; exit;
    	return ['gpa' => 0.00, 'grade' => 'F'];
    }
}

function grade_name_from_gpa($gpa){

  if($gpa == 5){
      return 'A+';
  }if($gpa >= 4){
      return 'A';
  }if($gpa >= 3.5){
      return 'A-';
  }if($gpa >= 3){
      return 'B';
  }if($gpa >= 2){
      return 'C';
  }if($gpa >= 1){
      return 'D';
  }if($gpa == 0){
      return 'F';
  }
}

$ALL = 'All Group';
$SCIENCE = 'Science';
$ARTS = 'Humanities';
$COMMERCE = 'Business Studies';

$GROUPS = [
  $ALL => $ALL,
  $SCIENCE => $SCIENCE,
  $ARTS => $ARTS,
  $COMMERCE => $COMMERCE
];


?>