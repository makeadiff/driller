<?php
$page_title = 'CPP Agreements';

$structure = array(
	'national'	=> array('name', 'user_count', 'agreement_percentage'),
	'city_id'	=> array(
						'center_id'		=> array('name', 'user_count', 'agreement_percentage'),
						'vertical_id'	=> array('name', 'user_count', 'agreement_percentage'),
					),
	'center_id'	=> array('name', 'user_count', 'agreement_percentage'),
	'all_in_city_id'	=> array('name', 'date', 'agreement'),
	'batch_id'	=> array('name', 'date', 'agreement'),
	'vertical_id'=>array('name', 'date', 'agreement'),
);

$cpp_agreement_model = new CPP_Agreement;

function getCollectiveData($all_units, $next_level_key, $extra_user_filter = array()) {
	global $model, $cpp_agreement_model;

	$data = array();

	foreach ($all_units as $row) {
		$id = $row['id'];

		$all_users = idNameFormat($model->getUsers(array_merge(array($next_level_key => $id), $extra_user_filter))); // Different data based on City, Center, Batch, etc.
		$user_count = count($all_users);
		if(!$user_count) continue;

		$agreements = $cpp_agreement_model->getAgreementStatus(array_keys($all_users));
		$agreed_users = count($agreements);
		$agreement_percentage = round($agreed_users / $user_count * 100, 2);

		$data_row = array(
			'id'	=> $id,
			'name'	=> $row['name'],
			'user_count' => $user_count,
			'agreement_percentage'	=> $agreement_percentage
		);

		$data[] = $data_row;
	}

	return $data;
}

function getIndividualData($users) {
	global $cpp_agreement_model, $config;

	$agreements = $cpp_agreement_model->getAgreementStatus(array_keys($users));
	
	$data = array();
	foreach ($users as $id => $name) {
		$data[] = array(
				'id'	=> $id,
				'name'	=> $name,
				'signed?' => (!empty($agreements[$id])) ? '<strong class="success">Yes</strong>' : '<strong class="error">No</strong>',
				'time_of_signing' => (!empty($agreements[$id])) ? date($config['time_format_php'], strtotime($agreements[$id])) : '',
			);
	}

	return $data;
}

