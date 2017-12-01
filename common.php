<?php
require '../common.php';

$year = 2017;
$model = new Common;

$is_event_id = i($QUERY, 'is_event_id', $model->getLatestISEvent());
$page_title = 'Impact Survey Reports';
