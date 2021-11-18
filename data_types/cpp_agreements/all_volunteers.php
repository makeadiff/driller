<?php
require dirname(dirname(dirname(dirname(__FILE__)))) . '/common/common.php';
$cpp_agreement_model = new CPP_Agreement;

$model = new Common;

$users = keyFormat($model->getUsers($QUERY));
$aggreement_status = $cpp_agreement_model->getAgreementStatus(array_keys($users));

showTop("Volunteer's CPP Agreement Status");
?><br /><br />
<h1 class="title">All Volunteer's CPP Agreement Status</h1>

<a href="<?php echo $config['site_home']; ?>?data_type=cpp_agreements">Back to Main Page</a>

<table class="table table-stripped">
	<tr><th>Count</th><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Signed On</th></tr>
	<?php 
	$count = 0;
	foreach ($users as $id => $user) {
		$count++;
		?>
		<tr><td><?php echo $count ?></td><td><?php echo $id ?></td><td><?php echo $user['name'] ?></td>
			<td><?php echo $user['email'] ?></td><td><?php echo $user['phone'] ?></td>
			<td><?php echo isset($aggreement_status[$id]) 
				? '<strong class="text-success">' . date('d M, Y', strtotime($aggreement_status[$id])) . '</strong>'
				: '<strong class="text-danger">Not Signed</strong>'; ?></td>
		</tr>
	<?php } ?>
</table>
<?php
showEnd();

