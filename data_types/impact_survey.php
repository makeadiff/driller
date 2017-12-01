<?php

$is_event_id = i($QUERY, 'is_event_id', $model->getLatestISEvent());
$page_title = 'Impact Survey Reports';

$all_questions = $model->getISQuestions();
$question_count = count($all_questions);
$final_level = 'teacher';
/*
$order = array('national', 'city_id', 'center_id', 'batch_id', 'teacher.php')
 */

function getData($all_units, $next_level_key) {
	global $model, $is_event_id, $question_count, $final_level;

	$data = array();

	foreach ($all_units as $row) {
		$id = $row['id'];

		$teacher_count = -1;
		if($next_level_key != $final_level) {
			$all_teachers = $model->getTeachers(array($next_level_key => $id)); // Different data based on City, Center, Batch, etc.
			$teacher_count = count($all_teachers);
			if(!$teacher_count) continue;
		} else {
			$all_teachers = array($id => $row['name']);
		}

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
		);
		if($teacher_count != -1) $data[$id]['teacher_count'] = $teacher_count;
		else $data[$id]['student_count'] = $total_student_count;

		$data[$id]['completion_percentage']	= $completion_percentage;
	}

	return $data;
}
