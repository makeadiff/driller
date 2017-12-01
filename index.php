<?php
require './common.php';

$data_type = i($QUERY, 'data_type');

if(!$data_type or !file_exists(joinPath('data_types', $data_type . '.php'))) die("Invalid Data Type");
require(joinPath('data_types', $data_type . '.php'));

$order = array_reverse(array_keys($structure));
$data = array();
foreach ($order as $parameter) {
	$id = i($QUERY, $parameter);
	if($id or $parameter == 'national') {
		$order_index = array_search($parameter, $order);
		$next_level_key = i($order, $order_index - 1);

		$page_title = getTitle($id, $parameter);
		$data = getListingData($parameter, $id);

		break;
	}
}

render();
