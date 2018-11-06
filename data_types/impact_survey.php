<?php
$ISParticipation = new Impact_Survey_Participation();

$all_questions = $ISParticipation->getISQuestions();
$question_count = count($all_questions);
$latest_is_event = i($QUERY, 'is_event_id', $ISParticipation->getLatestISEvent());
$is_event_id = $latest_is_event['id'];

$page_title = 'Impact Survey Adoption for ' . date('M Y', strtotime($latest_is_event['added_on']));

$structure = array(
	'national'	=> array('name', 'teacher_count', 'completion_percentage'),
	'city_id'	=> array(
						'center_id'		=> array('name', 'teacher_count', 'completion_percentage'),
					),
	'center_id'	=> array('name', 'teacher_count', 'completion_percentage'),
	'batch_id'	=> array('name', 'student_count', 'completion_percentage'),
	'user_id'	=> array('name', )
);

function getCollectiveData($all_units, $next_level_key, $extra_user_filter = []) {
	global $ISParticipation, $is_event_id, $question_count, $model;

	$data = [];

	foreach ($all_units as $row) {
		$id = $row['id'];

		$all_teachers = idNameFormat($model->getTeachers(array($next_level_key => $id))); // Different data based on City, Center, Batch, etc.
		$teacher_count = count($all_teachers);
		if(!$teacher_count) continue;

		$responses_count = $ISParticipation->getResponseCount(array_keys($all_teachers), $is_event_id);
		$students_count = $ISParticipation->getStudentCount(array_keys($all_teachers));

		$total_student_count = array_sum(array_values($students_count));
		$total_response_count = array_sum(array_values($responses_count));
		$possible_response_count = $total_student_count * $question_count;

		$completion_percentage = 0;
		if($total_response_count and $possible_response_count)
			$completion_percentage = round($total_response_count / $possible_response_count * 100, 2);

		$data[$id] = array(
			'id'	=> $id,
			'name'	=> $row['name'],
		);
		if($teacher_count != -1) $data[$id]['teacher_count'] = $teacher_count;
		else $data[$id]['student_count'] = $total_student_count;

		$data[$id]['completion_percentage']	= $completion_percentage;

	}

	return $data;
}

function getIndividualData($users) {
	global $ISParticipation, $is_event_id, $question_count;

	foreach ($users as $teacher_id => $teacher_name) {
		$responses_count = $ISParticipation->getResponseCount(array($teacher_id), $is_event_id);
		$students_count = $ISParticipation->getStudentCount(array($teacher_id));

		$total_student_count = array_sum(array_values($students_count));
		$total_response_count = array_sum(array_values($responses_count));
		$possible_response_count = $total_student_count * $question_count;

		$completion_percentage = 0;
		if($total_response_count and $possible_response_count)
			$completion_percentage = round($total_response_count / $possible_response_count * 100, 2);

		$data[$teacher_id] = [
			'id'						=> $teacher_id,
			'name'						=> $teacher_name,
			'students_count' 			=> $total_student_count,
			'completion_percentage'		=> $completion_percentage
		];
	}

	return [
		'metadata'	=> ['parameter' => 'user_id'],
		'data'		=> $data
	];
}

function getUserData($user_id) {
	global $model, $ISParticipation, $is_event_id;

	$data = [];
	$all_students = $model->getStudents($user_id);
	$all_questions = $ISParticipation->getISQuestions();

	foreach($all_students as $student) {
	 	$student_id = $student['id'];

	 	$responses = $ISParticipation->getISResponses($is_event_id, $user_id, $student_id);

	 	$response_data = [];
	 	foreach ($all_questions as $question_id => $question) {
	 		$response = i($responses, $question_id, 0);
	 		$response_data[] = array(
	 			'question'				=> $question,
	 			'response_percentage'	=> $response * 10
	 		);
	 	}

		$data[] = array(
			'title'		=> $student['name'],
			'metadata'	=> [],
			'data'		=> $response_data
		);
	}

	return $data;
}