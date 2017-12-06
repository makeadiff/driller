<h1><?php echo $page_title ?></h1>

<?php
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
				<td class="progress">
				<?php if($row[$key]) { ?><div class="complete" style="width:<?php echo $row[$key] ?>%;">&nbsp;</div><?php } ?>
				<?php if(100-$row[$key] > 0) { ?><div class="incomplete" style="width:<?php echo 100-$row[$key] ?>%;">&nbsp;</div><?php } ?>
				</td>
			<?php
			} else {
				echo "<td>$value</td>";
			}
		}
		?>
	</tr>
	<?php } ?>
</table>
<?php } ?>