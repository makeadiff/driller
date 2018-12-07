<?php
$page_title = 'CFR Participation';

$target = 12000;
$structure = array(
	'national'	=> array('name', 'user_count', 'number_of_users_who_reached_target', 'participation_percentage'),
	'city_id'	=> array(
						'center_id'		=> array('name', 'user_count', 'number_of_users_who_reached_target', 'participation_percentage'),
						'vertical_id'	=> array('name', 'user_count', 'number_of_users_who_reached_target', 'participation_percentage'),
					),
	'center_id'	=> array('name', 'user_count', 'participation_percentage'),
	'batch_id'	=> array('name', 'money_raised', 'donation_count'),
	'vertical_id'=>array('name', 'money_raised', 'donation_count'),
);

$participation_model = new CFR_Participation;

function getCollectiveData($all_units, $next_level_key, $extra_user_filter = array()) {
	global $model, $participation_model;

	$data = array();

	foreach ($all_units as $row) {
		$id = $row['id'];

		$all_users = idNameFormat($model->getUsers(array_merge(array($next_level_key => $id), $extra_user_filter))); // Different data based on City, Center, Batch, etc.
		$user_count = count($all_users);
		if(!$user_count) continue;

		$donations = $participation_model->getDonations(array_keys($all_users));
		$users_fundraised_count = count($donations);
		$participation_percentage = round($users_fundraised_count / $user_count * 100, 2);
		$users_who_reached_target_count = array_reduce($donations, function ($carry, $item) {
			global $target;
			if($item['total'] > $target) $carry++;

			return $carry;
		}, 0);
		$target_percentage = round($users_who_reached_target_count / $user_count * 100, 2);

		$data_row = array(
			'id'	=> $id,
			'name'	=> $row['name'],
			'user_count' => $user_count,
			'users_who_fundraised' => $users_fundraised_count,
			'number_of_users_who_reached_target' => $users_who_reached_target_count,
			'participation_percentage'	=> $participation_percentage,
		);

		$data[] = $data_row;
	}

	return $data;
}

function getIndividualData($users) {
	global $participation_model;

	$donations = $participation_model->getDonations(array_keys($users));
	$data = array();
	foreach ($users as $id => $name) {
		$data[] = array(
				'id'	=> $id,
				'name'	=> $name,
				'donations_count' => (!empty($donations[$id]['count'])) ? $donations[$id]['count'] : 0,
				'donations_total' => (!empty($donations[$id]['total'])) ? $donations[$id]['total'] : 0,
			);
	}

	return $data;
}

