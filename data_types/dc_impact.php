<?php
$survey_model = new Survey();

$survey_id = i($QUERY, 'survey_id');
if(!$survey_id) die("Please provide the survey id for this report.");

$survey = $survey_model->getSurvey($survey_id);
$all_questions = $survey_model->getQuestions($survey['template_id']);

$question_count = count($all_questions);

$page_title = 'DC Impact Survey Adoption for ' . $survey['name'] . ' ' . $survey['template_name'];

$structure = array(
	'national'	=> array('name', 'student_count', 'completion_percentage'),
	'city_id'	=> array(
						'center_id'		=> array('name', 'responded'),
					),
	'center_id'	=> array('name', 'completion_percentage'),
	'student_id'=> array('name', 'responded')
);

function getCollectiveData($all_units, $next_level_key, $extra_user_filter = []) {
	global $survey_model, $survey_id, $question_count, $model;

	$data = [];

	foreach ($all_units as $row) {
		$id = $row['id'];

		if($next_level_key == 'city_id') {
			$all_students = idNameFormat($model->getStudentsInCity($id));
		} elseif($next_level_key == 'center_id') {
			$all_students = idNameFormat($model->getStudentsInCenter($id));
		} elseif($next_level_key == 'student_id') {
			$all_students = idNameFormat($all_units);
		} else {
			die("$next_level_key not supported in this report");
		}

		if($next_level_key == 'student_id') {
			$responses_count = $survey_model->getResponseCount($survey_id, [$row['id']]);
			$student_count = 1;
		} else {
			$responses_count = $survey_model->getResponseCount($survey_id, array_keys($all_students));
			$student_count = count($all_students);
		}
		if(!$student_count) continue;

		$total_response_count = array_sum(array_values($responses_count));
		$possible_response_count = $student_count * $question_count;

		$completion_percentage = 0;
		if($total_response_count and $possible_response_count)
			$completion_percentage = round($total_response_count / $possible_response_count * 100, 2);

		$data[$id] = array(
			'id'	=> $id,
			'name'	=> $row['name'],
		);
		if($student_count != -1) $data[$id]['student_count'] = $student_count;
		else $data[$id]['student_count'] = $student_count;

		$data[$id]['completion_percentage']	= $completion_percentage;
	}

	return $data;
}

function getStudentData($student_id) {
	global $model, $survey_model, $survey_id, $all_questions;

	$data = [];
 	$student = $model->getStudent($student_id);

 	$responses = $survey_model->getResponses($survey_id, $student_id);
 	
 	$response_data = [];
 	$ratio = 100 / count($all_questions);
 	foreach ($all_questions as $question_id => $question) {
 		$response = isset($responses[$question_id]) ? 1 : 0;
 		$response_data[] = array(
 			'question'	=> $question,
 			'responded'	=> ($response) ? '<strong class="success-message">Yes</strong>' : '<strong class="error-message">No</strong>'
 		);
 	}

	$data[] = array(
		'title'		=> $student['name'],
		'metadata'	=> [],
		'data'		=> $response_data
	);

	return $data;
}