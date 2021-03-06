<h1><?php echo $page_title ?></h1>

<?php
if($current_level == 'city_id') {
	$parameters = ['all_in_city_id' => $QUERY['city_id'], 'data_type' => $data_type];
	if(isset($QUERY['event_type_id'])) $parameters['event_type_id'] = i($QUERY, 'event_type_id');
	echo "<a href='" . getLink('index.php', $parameters) . "'>Show All Volunteers</a><br />";
}
if(isset($custom_pages)) {
	foreach ($custom_pages as $page => $title) {
		echo "<a href='" . getLink($page, array(), true) . "'>$title</a><br />";
	}
}

$total_row = array();
foreach($data as $data_row) {
	$title = i($data_row, 'title');

	$first_row = reset($data_row['data']);
	if($first_row) {
		if($title) echo "<h3>$title</h3>";
		$width = 100 / count($first_row);
	} else {
		$width = 0;
	}
	?>

<table class="table table-striped">
	<tr>
		<?php
		if($first_row) {
			foreach ($first_row as $key => $value) {
				if($key == 'id') continue;
				elseif($key == 'name') $total_row[$key] = 'National';
				else {
					$total_row[$key] = 0;
				}
	
				echo "<th width='$width%'>" . format($key) . "</th>";
			} 
		}
		?>
	</tr>
	<?php foreach ($data_row['data'] as $row) { ?>
	<tr>
		<?php
		foreach ($row as $key => $value) {
			if($key == 'id') continue;
			if($key == 'name' and $data_row['metadata']['parameter']) {
				echo "<td><a href='" . getLink('', array($data_row['metadata']['parameter'] => $row['id']), true) . "'>$value</a></td>";
			
			} elseif((stripos($key, 'percent') !== false or stripos($key, '%') !== false) and is_numeric($row[$key])) { 
				if(stripos($key, 'percent') !== false) { ?>
				<td class="progress" title="<?php echo $row[$key] ?>%">
				<?php if($row[$key]) { ?><div class="complete" style="width:<?php echo $row[$key] ?>%;">&nbsp;</div><?php } ?>
				<?php if(100 - $row[$key] > 0) { ?><div class="incomplete" style="width:<?php echo 100 - $row[$key] ?>%;">&nbsp;</div><?php } ?>
				</td>
				<?php
				} else {
					echo "<td>$value%</td>";
				}
				if(!isset($total_row[$key. '_total'])) {
					$total_row[$key. '_total'] = 0;
					$total_row[$key. '_count'] = 0;
				}
				$total_row[$key. '_total'] += $row[$key];
				$total_row[$key. '_count'] ++;
			} else {
				if(is_numeric($value)) $total_row[$key] += $value;
				$attr = '';
				if($key == 'name' and isset($row['id'])) $attr = " title='ID: $row[id]'";
				echo "<td$attr>$value</td>";
			}
		}
		?>
	</tr>
	<?php }
	echo "<tr>";
	if($first_row) {
		foreach($first_row as $key => $value) {
			if($key == 'id') continue;
			
			if(stripos($key, 'percent')) {
				$value = round($total_row[$key . '_total'] / $total_row[$key . '_count'], 2);
				?>
				<td class="progress" title="<?php echo $value ?>%">
					<?php if($value) { ?><div class="complete" style="width:<?php echo $value ?>%;">&nbsp;</div><?php } ?>
					<?php if(100 - $value > 0) { ?><div class="incomplete" style="width:<?php echo 100 - $value ?>%;">&nbsp;</div><?php } ?>
				</td>
				<?php
			} elseif(stripos($key, '%')) {
				$value = round($total_row[$key . '_total'] / $total_row[$key . '_count'], 2);
				echo "<td><strong>" . $value . "%</strong></td>";
			} else {
				$value = $total_row[$key];

				if($value == 'National' and i($QUERY, 'city_id') === false) {
					$value = '<a href="' . getLink('', array($data_row['metadata']['parameter'] => 0), true) . "\">$value</a>";
				}

				if($value == 'National' and $current_level != 'national') $value = 'Total';

				echo "<td><strong>" . $value . "</strong></td>";
			}
		}
	}
	echo "</tr>";
	?>
</table>
<?php } ?>