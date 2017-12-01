<h1><?php echo $page_title ?></h1>

<?php $first_row = reset($data); ?>

<table class="table table-striped">
	<tr><th>Name</th>
		<?php if($first_row['teacher_count'] != -1) { ?><th>Teacher Count</th><?php } ?>
		<th colspan="2" width="200">Completion</th></tr>
	<?php foreach ($data as $row) { ?>
	<tr>
		<td><?php if($next_level_key == 'teacher') { ?><a href="teacher.php?user_id=<?php echo $row['id'] ?>">
			<?php } else { ?><a href="index.php?<?php echo $next_level_key ?>=<?php echo $row['id'] ?>">
			<?php } 
				echo $row['name'] ?></a></td>
		<?php if($first_row['teacher_count'] != -1) { ?><td><?php echo $row['teacher_count'] ?></td><?php } ?>
		<td width="30"><?php echo $row['completion_percentage'] ?>%</td>
		<td class="progress">
<?php if($row['completion_percentage']) { ?><div class="complete" style="width:<?php echo $row['completion_percentage'] ?>%;">&nbsp;</div><?php } ?>
<?php if(100-$row['completion_percentage'] > 0) { ?><div class="incomplete" style="width:<?php echo 100-$row['completion_percentage'] ?>%;">&nbsp;</div><?php } ?>
</div></td>
	</tr>
	<?php } ?>
</table>
