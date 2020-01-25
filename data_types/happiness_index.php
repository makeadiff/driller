<?php
$page_title = 'Happiness Index';

$structure = array(
	'national'	=> array('name', 'user_count', 'filled', 'average_rating'),
	'city_id'	=> array(
						'center_id'		=> array('name', 'user_count', 'filled', 'average_rating'),
						'vertical_id'	=> array('name', 'user_count', 'filled', 'average_rating'),
					),
	'center_id'	=> array('name', 'user_count', 'filled', 'average_rating'),
	'batch_id'	=> array('name', 'date', 'filled', 'average_rating'),
	'vertical_id'=>array('name', 'date', 'filled', 'average_rating'),
);
// $custom_pages = ['data_types/cpp_agreements/list_volunteers_not_signed.php' => 'List all volunteers who have not signed the agreement'];

$hi_model = new Survey;
$survey_id = 82;

function getCollectiveData($all_units, $next_level_key, $extra_user_filter = []) {
	global $model, $hi_model, $survey_id;

	$data = [];

	foreach ($all_units as $row) {
		$id = $row['id'];

		$all_users = idNameFormat($model->getUsers(array_merge([$next_level_key => $id], $extra_user_filter))); // Different data based on City, Center, Batch, etc.
		$user_count = count($all_users);

		if(!$user_count) continue;

		$filled = $hi_model->getResponses($survey_id, array_keys($all_users));
		$filled_users = count($filled);
		// $response_sum = array_reduce($filled, function($carried, $value) {
		// 	return ($carried + $value['average_response']);
		// }, 0);
		// $average_rating = 0;
		// if($filled_users) $average_rating = round($response_sum / $filled_users, 2);

		$filled_percent = round($filled_users / $user_count * 100, 2);

		$data_row = [
			'id'			=> $id,
			'name'			=> $row['name'],
			'user_count' 	=> $user_count,
			'filled'		=> $filled_users,
			// 'average_rating'=> $average_rating,
			'filled_%'		=> $filled_percent,
		];

		$data[] = $data_row;
	}
	usort($data, function($a, $b) {
		if($a['filled_%'] == $b['filled_%']) return 0;
		return ($a['filled_%'] < $b['filled_%']) ? -1 : 1;
	});

	return $data;
}

function getIndividualData($users) {
	global $hi_model, $config, $survey_id;

	$responses = $hi_model->getResponses($survey_id, array_keys($users));
	
	$data = [];
	foreach ($users as $id => $name) {
		$data[] = [
			'id'	=> $id,
			'name'	=> "<a href='../../apps/damrof/individual_responses.php?survey_id=$survey_id&responder_id=$id'>$name</a>",
			'filled'=> isset($responses[$id]) ? 'Yes' : 'No',
			// 'average_rating' => isset($responses[$id]) ? round($responses[$id]['average_response'], 2) : '',
			// 'time_of_signing' => (!empty($responses[$id])) ? date($config['time_format_php'], strtotime($responses[$id])) : '',
		];
	}

	return $data;
}

