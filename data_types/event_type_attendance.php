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
$starts_on = i($QUERY, 'starts_on');
$ends_on = i($QUERY, 'ends_on');
if($starts_on) $event_model->starts_on = $starts_on . ' 00:00:00';
if($ends_on) $event_model->ends_on = $ends_on . ' 23:59:59';
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

		$attended = $event_model->getCollectiveStatus($event_type_id, array_keys($all_users));
		$invited_count = count($attended);
		$attended_count = array_reduce($attended, function($carry, $item) {
			if($item['present'] == '1') $carry++; // Count all the people who actually came.
			return $carry;
		}, 0);


		$attended_percentage = 0;
		if($invited_count and $attended_count) $attended_percentage = round($attended_count / $invited_count * 100, 2);
		$unique_event_count = array_reduce($attended, function($carry, $item) {
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
			'event_count'			=> $unique_event_count,
			// 'user_count' 			=> $user_count,
			'invited'	 			=> $invited_count,
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

