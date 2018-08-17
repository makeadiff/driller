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
$vlc_event_type_id = 1;

$page_title = $event_model->getEventType($vlc_event_type_id);


function getCollectiveData($all_units, $next_level_key, $extra_user_filter = array()) {
	global $model, $event_model, $vlc_event_type_id;

	$data = array();

	foreach ($all_units as $row) {
		$id = $row['id'];

		$user_search_parameters = array_merge([$next_level_key => $id], $extra_user_filter);
		// dump($user_search_parameters);
		$all_users = keyFormat($model->getUsers($user_search_parameters, ['U.id', 'U.name', 'U.joined_on'])); // Different data based on City, Center, Batch, etc.
		$user_count = count($all_users);
		if(!$user_count) continue;

		$attended = keyFormat($event_model->getCollectiveStatus($vlc_event_type_id, array_keys($all_users), '1'));
		$new_vols = 0;
		$continuing_vols = 0;
		foreach ($all_users as $user_id => $user) {
			if(isset($attended[$user_id])) { // This user hasn't attended. Check if old vol.
				if($user['joined_on'] < '2018-01-01') {
					$continuing_vols++;
				} else {
					$new_vols++;
				}
			}
		}

		$attended_count = count($attended);
		$new_to_old_ratio = 0;
		if($continuing_vols) $new_to_old_ratio = round($new_vols / ($continuing_vols + $new_vols) * 100, 2);
		if($new_to_old_ratio > 100) $new_to_old_ratio = 100;

		$data[] = array(
			'id'					=> $id,
			'name'					=> $row['name'],
			'user_count' 			=> $user_count,
			'attended'	 			=> $attended_count,
			'new_volunteers'		=> $new_vols,
			'continuing_volunteers'	=> $continuing_vols,
			'new_to_old_ratio_percentage'	=> $new_to_old_ratio
		);
	}

	return $data;
}

function getIndividualData($users) {
	global $event_model, $vlc_event_type_id, $config;

	$attendance = keyFormat($event_model->getCollectiveStatus($vlc_event_type_id, array_keys($users)));
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

