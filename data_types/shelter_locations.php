<?php
$page_title = 'Shelter Locations';

$structure = array(
	'national'	=> array('name', 'shelters_count', 'shelters_located', 'located_percentage'),
	'city_id'	=> array('name', 'located'),
);
// $custom_pages = ['data_types/cpp_agreements/list_volunteers_not_signed.php' => 'List all volunteers who have not signed the agreement'];

function getCollectiveData($all_units, $next_level_key, $extra_user_filter = array()) {
	global $model, $sql;

	$data = array();

	foreach ($all_units as $row) {
		$id = $row['id'];

		$all_shelters = $sql->getById("SELECT id,name,latitude FROM Center WHERE status='1' AND name NOT LIKE 'Aftercare%' AND $next_level_key=$id");

		$shelters_count = count($all_shelters);
		if(!$shelters_count) continue;

		$shelters_located = 0;
		foreach($all_shelters as $shelter) 
			if($shelter['latitude']) 
				$shelters_located++;

		$located_percentage = round($shelters_located / $shelters_count * 100, 2);

		$data_row = array(
			'id'				=> $id,
			'name'				=> $row['name'],
			'shelters_count' 	=> $shelters_count,
			'shelters_located'	=> $shelters_located,
			'located_percentage'=> $located_percentage,
		);

		$data[] = $data_row;
	}

	return $data;
}

function getIndividualData($shelters) {
	global $config, $sql;

	$shelter_ids = array_keys(idNameFormat($shelters));

	$located = $sql->getById("SELECT id,name,latitude FROM Center WHERE id IN (" . implode(",", $shelter_ids) . ")");
	
	$data = array();
	foreach ($shelters as $shelter) {
		$data[] = array(
				'id'	=> $shelter['id'],
				'name'	=> $shelter['name'],
				'located' => ($located[$shelter['id']]['latitude']) ? '<strong class="success">Yes</strong>' : '<strong class="error">No</strong>',
			);
	}

	return $data;
}

 
