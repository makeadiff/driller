<?php
require dirname(dirname(dirname(__DIR__))) . '/commons/common.php';
$cpp_agreement_model = new CPP_Agreement;

$model = new Common;

$users = keyFormat($model->getUsers($QUERY));
$aggreement_status = $cpp_agreement_model->getAgreementStatus(array_keys($users));

showTop("Volunteers Who Haven't Signed the CPP Agreement");
?><br /><br />
<h1 class="title">Volunteers Who Haven't Signed the CPP Agreement</h1>

<a href="<?php echo $config['app_url']; ?>?data_type=cpp_agreements">Back to Main Page</a>

<table class="table table-stripped">
	<tr><th>Count</th><th>ID</th><th>Name</th><th>Email</th><th>Phone</th></tr>
	<?php 
	$count = 0;
	foreach ($users as $id => $user) {
		if(isset($aggreement_status[$id])) continue;
		$count++;
		?>
		<tr><td><?php echo $count ?></td><td><?php echo $id ?></td><td><?php echo $user['name'] ?></td><td><?php echo $user['email'] ?></td><td><?php echo $user['phone'] ?></td></tr>
	<?php } ?>
</table>
<?php
showEnd();

