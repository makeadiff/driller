<?php
require './common.php';

$user_id = i($QUERY, 'user_id');
if(!$user_id) die("No teacher specified");

$all_questions = $model->getISQuestions();

$name = $model->getTeacherName($user_id);
$page_title .= " : $name";

$data = array();
$all_students = $model->getStudents($user_id);

foreach($all_students as $student) {
 	$student_id = $student['id'];

 	$responses = $model->getISResponses($is_event_id, $user_id, $student_id);

	$data[$student_id] = array(
		'id'		=> $student_id,
		'name'		=> $student['name'],
		'responses'	=> $responses
	);
}

render();
