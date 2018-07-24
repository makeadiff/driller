<?php
$event_model = new Event;

$structure = array(
	'national'	=> array('name', 'user_count', 'attended_percentage'),
	'city_id'	=> array(
						'center_id'		=> array('name', 'user_count', 'attended_percentage'),
						'vertical_id'	=> array('name', 'user_count', 'attended_percentage'),
					),
	'center_id'	=> array('name', 'user_count', 'attended_percentage'),
	'batch_id'	=> array('name', 'user_count', 'attended_percentage'),
	'vertical_id'=>array('name', 'user_count', 'attended_percentage'),
);
$event_type_id = i($QUERY, 'event_type_id');
if(!$event_type_id) die("Please provide the event_type_id");

$page_title = $event_model->getEventType($event_type_id);


function getCollectiveData($all_units, $next_level_key, $extra_user_filter = array()) {
	global $model, $event_model, $event_type_id;

	$data = array();

	foreach ($all_units as $row) {
		$id = $row['id'];

		$user_search_parameters = array_merge([$next_level_key => $id], $extra_user_filter);
		// dump($user_search_parameters);
		$all_users = idNameFormat($model->getUsers($user_search_parameters)); // Different data based on City, Center, Batch, etc.
		$user_count = count($all_users);
		if(!$user_count) continue;

		$attended = $event_model->getCollectiveStatus($event_type_id, array_keys($all_users), '1');
		$attended_count = count($attended);
		$attended_percentage = round($attended_count / $user_count * 100, 2);

		$data[] = array(
			'id'					=> $id,
			'name'					=> $row['name'],
			'user_count' 			=> $user_count,
			'attended'	 			=> $attended_count,
			'attended_percentage'	=> $attended_percentage
		);
	}

	return $data;
}

function getIndividualData($users) {
	global $event_model, $event_type_id, $config;

	$attendance = keyFormat($event_model->getCollectiveStatus($event_type_id, array_keys($users)));
	$data = array(); 
	foreach ($users as $id => $name) {
		$data[$id] = [
			'id'	=> $id,
			'name'	=> $name,
			'attended' 	=> '',
			'event_date'=> '',
		];

		$event_info = '';
		if(isset($attendance[$id]) and $attendance[$id]['present'] == '1') {
			$event_info = "<a href='../envite/attendance.php?event_id={$attendance[$id]['event_id']}'>" . date($config['time_format_php'], strtotime($attendance[$id]['starts_on'])) . "</a>";
		}

		if(isset($attendance[$id]))	{
			$data[$id]['attended'] 		= ($attendance[$id]['present'] == '1') ? '<span class="success">Yes</span>' : '<span class="error">No</span>';
			$data[$id]['attended_on'] 	= $event_info;
		}
	}

	return $data;
}

