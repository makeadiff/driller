<?php
require './common.php';

$city_id = i($QUERY, 'city_id');
$center_id = i($QUERY, 'center_id');
$batch_id = i($QUERY, 'batch_id');
$batch_id = i($QUERY, 'batch_id');

$all_questions = $model->getISQuestions();
$question_count = count($all_questions);

if($city_id) {
	$name = $model->getCityName($city_id);
	$page_title .= " : $name";
	$next_level_key = 'center_id';

	$all_centers = $model->getCenters($city_id);
	$data = getData($all_centers, $next_level_key);

} elseif($center_id) {
	$name = $model->getCenterName($center_id);
	$page_title .= " : $name";
	$next_level_key = 'batch_id';

	$all_batches = $model->getBatches($center_id);
	$data = getData($all_batches, $next_level_key);

} elseif ($batch_id) {
	$name = $model->getBatchName($batch_id);
	$page_title .= " : $name";
	$next_level_key = 'teacher';

	$data = array();
	$all_teachers = $model->getTeachers(array('batch_id' => $batch_id));
	foreach ($all_teachers as $teacher_id => $teacher_name) {
		$responses_count = $model->getResponseCount(array($teacher_id), $is_event_id);
		$students_count = $model->getStudentCount(array($teacher_id));

		$total_student_count = array_sum(array_values($students_count));
		$total_response_count = array_sum(array_values($responses_count));
		$possible_response_count = $total_student_count * $question_count;

		$completion_percentage = 0;
		if($total_response_count and $possible_response_count)
			$completion_percentage = round($total_response_count / $possible_response_count * 100, 2);


		$data[$teacher_id] = array(
			'id'						=> $teacher_id,
			'name'						=> $teacher_name,
			'teacher_count'				=> -1,
			'total_response_count' 		=> $total_response_count,
			'possible_response_count' 	=> $possible_response_count,
			'completion_percentage'		=> $completion_percentage
		);
	}
} else { // National
	$page_title .= " : National";
	$next_level_key = 'city_id';

	$all_cities = $model->getCities();
	$data = getData($all_cities, $next_level_key);
}

render();

function getData($all_units, $next_level_key) {
	global $model, $is_event_id, $question_count;

	$data = array();

	foreach ($all_units as $row) {
		$id = $row['id'];

		$all_teachers = $model->getTeachers(array($next_level_key => $id)); // Different data based on City, Center, Batch, etc.
		$teacher_count = count($all_teachers);
		if(!$teacher_count) continue;

		$responses_count = $model->getResponseCount(array_keys($all_teachers), $is_event_id);
		$students_count = $model->getStudentCount(array_keys($all_teachers));

		$total_student_count = array_sum(array_values($students_count));
		$total_response_count = array_sum(array_values($responses_count));
		$possible_response_count = $total_student_count * $question_count;

		$completion_percentage = 0;
		if($total_response_count and $possible_response_count)
			$completion_percentage = round($total_response_count / $possible_response_count * 100, 2);

		$data[$id] = array(
			'id'						=> $id,
			'name'						=> $row['name'],
			'teacher_count' 			=> $teacher_count,
			'total_response_count' 		=> $total_response_count,
			'possible_response_count' 	=> $possible_response_count,
			'completion_percentage'		=> $completion_percentage
		);
	}

	return $data;
}
