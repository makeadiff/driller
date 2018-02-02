<h1><?php echo $page_title ?></h1>

<?php
if($current_level == 'city_id') {
	echo "<a href='?all_in_city_id=$QUERY[city_id]&data_type=$data_type'>Show All Volunteers</a><br />";
}
if(isset($custom_pages)) {
	foreach ($custom_pages as $page => $title) {
		echo "<a href='" . getLink($page, array(), true) . "'>$title</a><br />";
	}
}

$total_row = array();
foreach($data as $data_row) {
	$title = i($data_row, 'title');
	if($title) echo "<h3>$title</h3>";

	$first_row = reset($data_row['data']);
	$width = 100 / count($first_row);
	?>

<table class="table table-striped">
	<tr>
		<?php 
		foreach ($first_row as $key => $value) {
			if($key == 'id') continue;
			elseif($key == 'name') $total_row[$key] = 'Total';
			else {
				$total_row[$key] = 0;
			}

			echo "<th width='$width%'>" . format($key) . "</th>";
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
			
			} elseif(stripos($key, 'percent')) { ?>
				<td class="progress" title="<?php echo $row[$key] ?>%">
				<?php if($row[$key]) { ?><div class="complete" style="width:<?php echo $row[$key] ?>%;">&nbsp;</div><?php } ?>
				<?php if(100-$row[$key] > 0) { ?><div class="incomplete" style="width:<?php echo 100-$row[$key] ?>%;">&nbsp;</div><?php } ?>
				</td>
			<?php
				if(!isset($total_row[$key. '_total'])) {
					$total_row[$key. '_total'] = 0;
					$total_row[$key. '_count'] = 0;
				}
				$total_row[$key. '_total'] += $row[$key];
				$total_row[$key. '_count'] ++;
			} else {
				if(is_numeric($value)) $total_row[$key] += $value;
				echo "<td>$value</td>";
			}
		}
		?>
	</tr>
	<?php }
	echo "<tr>";
	foreach($first_row as $key => $value) {
		if($key == 'id') continue;
		
		if(stripos($key, 'percent')) {
			$value = round($total_row[$key . '_total'] / $total_row[$key . '_count'], 2);
			?>
			<td class="progress" title="<?php echo $value ?>%">
				<?php if($value) { ?><div class="complete" style="width:<?php echo $value ?>%;">&nbsp;</div><?php } ?>
				<?php if(100-$value > 0) { ?><div class="incomplete" style="width:<?php echo 100-$value ?>%;">&nbsp;</div><?php } ?>
			</td>
			<?php
		} else {
			$value = $total_row[$key];

			echo "<td><strong>" . $value . "</storng></td>";
		}
	}
	echo "</tr>";
	?>
</table>
<?php } ?>