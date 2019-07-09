<?php
require './common.php';

$no_cache = i($QUERY, 'no_cache', false);
$data_type = i($QUERY, 'data_type');

if(!$data_type or !file_exists(joinPath('data_types', $data_type . '.php'))) die("Invalid Data Type");
require(joinPath('data_types', $data_type . '.php'));

$order = array_reverse(array_keys($structure));
$data = array();
$current_level = 'national';

foreach ($order as $parameter) {
	$id = i($QUERY, $parameter);

	if($id !== false or $parameter == 'national') {
		$order_index = array_search($parameter, $order);
		$next_level_key = i($order, $order_index - 1);

		$page_title = getTitle($id, $parameter);

		list($data, $cache_key) = getCacheAndKey('Driller', ['data_type' => $data_type, 'parameter' => $parameter, 'id' => $id]);
		if(!$data or 1) { // :TODO: Cache disabled
			$data = getListingData($parameter, $id);
			setCache($cache_key, $data);
		}
		$current_level = $parameter;

		break;
	}
}

render();
