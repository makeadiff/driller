<?php

function getTitle($id, $type) {
	global $model, $page_title;

	if($type == 'batch_id') {
		$name = $model->getBatchName($id);
	} elseif($type == 'center_id') {
		$name = $model->getCenterName($id);
	} elseif($type == 'city_id') {
		$name = $model->getCityName($id);
	} elseif($type == 'vertical_id') {
		$name = $model->getVerticalName($id);
	} else { // National
		$name = "National";
	}

	return $page_title . ' : ' . $name;
}

function getListingData($parameter, $id) {
	global $model, $page_title, $structure, $order, $QUERY;

	$order_index = array_search($parameter, $order);
	$next_level_key = i($order, $order_index - 1);

	$data = array();
	$split_data = array();

	if($parameter == 'batch_id') {
		$all_teachers = idNameFormat($model->getTeachers(array('batch_id' => $id)));
		$next_level_key = '';
		$data = getIndividualData($all_teachers);

	} elseif($parameter == 'center_id') {
		$center_id = i($QUERY, 'center_id');
		$all_batches = $model->getBatches($center_id);
		$data = getCollectiveData($all_batches, $next_level_key);

	} elseif($parameter == 'vertical_id') {
		$city_id = i($QUERY, 'city_id', 0);
		$all_users = idNameFormat($model->getUsers(array('vertical_id' => $id, 'city_id' => $city_id)));
		$next_level_key = '';
		$data = getIndividualData($all_users);

	} elseif($parameter == 'city_id') {
		if(isAssoc($structure[$parameter])) {
			foreach ($structure[$parameter] as $key => $value) {
				if($key == 'vertical_id') {
					$all_verticals = $model->getVerticals($id);

					$split_data[] = array(
						'metadata'	=> array('parameter' => 'vertical_id'),
						'title'		=> 'Verticals',
						'data'		=> getCollectiveData($all_verticals, $key, array('city_id' => $id))
					);

				} elseif($key == 'center_id') {
					$all_centers = $model->getCenters($id);
					$split_data[] = array(
						'metadata' 	=> array('parameter' => 'center_id'),
						'title'		=> 'Centers',
						'data'		=> getCollectiveData($all_centers, $key, array('city_id' => $id))
					);
				}
			}
		} else {
			$all_centers = $model->getCenters($city_id);
			$data = getCollectiveData($all_centers, $next_level_key);
		}

	} elseif($parameter == 'national') { // National
		$all_cities = $model->getCities();
		$data = getCollectiveData($all_cities, $next_level_key);
	}

	if(!$data and $split_data) {
		$return_data = $split_data;

	} elseif($data and !$split_data) {
		$return_data = array(array(
			'metadata'	=> array('parameter' => $next_level_key),
			'title'		=> '',
			'data'		=> $data
		));
	} else {
		$return_data = $data;
	}

	return $return_data;
}

function isAssoc(array $arr) {
    if (array() === $arr) return false;
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function idNameFormat($data, $fields=array('id','name')) {
	$return = array();
	foreach($data as $row) {
		if(isset($fields[1])) $return[$row[$fields[0]]] = stripslashes($row[$fields[1]]);
		else $return[$row[$fields[0]]] = $row;
	}
	
	return $return;
}

function oneFormat($data) {
	return current($data);
}