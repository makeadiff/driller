<?php
$event_model = new Event;

$structure = array(
	'national'	=> array('name', 'user_count', 'coming_%'),
	'city_id'	=> array(
						'center_id'		=> array('name', 'user_count', 'coming_%'),
						'vertical_id'	=> array('name', 'user_count', 'coming_%'),
					),
	'center_id'	=> array('name', 'user_count', 'coming_%'),
	'batch_id'	=> array('name', 'user_count', 'coming_%'),
	'vertical_id'=>array('name', 'user_count', 'coming_%'),
);
$event_id = i($QUERY, 'event_id');
$starts_on = i($QUERY, 'starts_on');
$ends_on = i($QUERY, 'ends_on');
if($starts_on) $event_model->starts_on = $starts_on . ' 00:00:00';
if($ends_on) $event_model->ends_on = $ends_on . ' 23:59:59';
if(!$event_id) die("Please provide the event_id");

$event = $event_model->getEvent($event_id);
$page_title = $event['name'];

function getCollectiveData($all_units, $next_level_key, $extra_user_filter = array()) {
	global $model, $event_model, $event_id;

	$data = array();

	foreach ($all_units as $row) {
		$id = $row['id'];

		$user_search_parameters = array_merge([$next_level_key => $id], $extra_user_filter);
		// dump($user_search_parameters);
		$all_users = idNameFormat($model->getUsers($user_search_parameters)); // Different data based on City, Center, Batch, etc.
		$user_count = count($all_users);
		if(!$user_count) continue;

		$event_attendance = $event_model->getCollectiveStatus($event_id, array_keys($all_users));
		$invited_count = count($event_attendance);
		$coming_count = array_reduce($event_attendance, function($carry, $item) {
			if($item['user_choice'] == '1') $carry++; // Count all the people who actually came.
			return $carry;
		}, 0);


		$coming_percentage = 0;
		if($invited_count and $coming_count) $coming_percentage = round($coming_count / $invited_count * 100, 2);
		$unique_event_count = array_reduce($event_attendance, function($carry, $item) {
			static $event_ids = [];

			if(!in_array($item['event_id'], $event_ids)) {
				$event_ids[] = $item['event_id'];
				$carry++;
			}

			return $carry;
		}, 0);

		$data[] = array(
			'id'					=> $id,
			'name'					=> $row['name'],
			'user_count' 			=> $user_count,
			'invited'	 			=> $invited_count,
			'coming'	 			=> $coming_count,
			'coming_%'	=> $coming_percentage
		);
	}

	return $data;
}

function getIndividualData($users) {
	global $event_model, $event_id, $config;

	$attendance = keyFormat($event_model->getCollectiveStatus($event_id, array_keys($users)));
	$data = array(); 
	foreach ($users as $id => $name) {
		$data[$id] = [
			'id'	=> $id,
			'name'	=> $name,
			'coming' 	=> '',
		];

		if(isset($attendance[$id]))	{
			$data[$id]['coming'] 		= ($attendance[$id]['user_choice'] == '1') ? '<span class="success">Yes</span>' : '<span class="error">No</span>';
		}
	}

	return $data;
}

