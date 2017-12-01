<h1><?php echo $page_title ?></h1>


<?php foreach ($data as $row) { ?>
<h3><?php echo $row['name'] ?></h3>

<table class="table table-striped">
	<tr><th>Question</th><th>Response</th></tr>

<?php foreach ($all_questions as $question_id => $question) { ?>
<tr>
	<td><?php echo $question ?></td>
	<td class="progress"><?php 
		$response = i($row['responses'], $question_id, 0);
		$score = $response * 10;

		if(!$score) echo 'No Data';
		else {
			if($score) { ?><div class="complete" style="width:<?php echo $score ?>%;">&nbsp;</div><?php } 
			if(100-$score > 0) { ?><div class="incomplete" style="width:<?php echo 100-$score ?>%;">&nbsp;</div><?php }
		}
	?></td>
</tr>
<?php } ?>
</table>

<?php } ?>