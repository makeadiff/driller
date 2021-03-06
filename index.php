<?php
require './common.php';

$no_cache = i($QUERY, 'no_cache', false);
$data_type = i($QUERY, 'data_type');

if(!$data_type or !file_exists(joinPath('data_types', $data_type . '.php'))) {
	$all_data_types = ls('*.php', joinPath($config['site_folder'], 'data_types'));
	render('list_data_types.php');
	exit;
}
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
		if(!$data or 1) { // add a ' or 1' to disable Cache
			$data = getListingData($parameter, $id);
			setCache($cache_key, $data);
		}
		$current_level = $parameter;

		break;
	}
}

render();
