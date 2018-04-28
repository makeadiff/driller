<?php
$page_title = 'Ed Support Requirement Collection';

$structure = array(
	'national'	=> array('name', 'requirement', 'buffer', 'teachers_assigned'),
	'city_id'	=> array(
						'center_id'		=> array('name', 'requirement', 'buffer', 'teachers_assigned'),
					),
	'center_id'	=> array('name', 'requirement', 'teachers_assigned'),
	// 'batch_id'	=> array('name', 'requirement', 'teachers_assigned'),
);

$buffer_percentage = 40 / 100;

// $participation_model = new CFR_Participation;

function getCollectiveData($all_units, $next_level_key, $extra_user_filter = array()) {
	global $model, $sql, $year, $buffer_percentage;

	$data = array();

	foreach ($all_units as $row) {
		$id = $row['id'];
		$requirement = 0;
		$assigned_teachers = [];
		$name = '';

		if(!$next_level_key) { // We are at batch level
			$name = $model->getBatchName($id);

			$requirement = findRequirementInBatch($id);
			$assigned_teachers = $model->getTeachers(['batch_id' => $id]);

		} elseif($next_level_key == 'center_id') {
			$name = $row['name'];
			$assigned_teachers = $model->getTeachers(['center_id' => $id]);
			
			$batches = $model->getBatches($id);
			$requirement = 0;
			foreach ($batches as $batch) {
				$requirement += findRequirementInBatch($batch['id']);
			}

		} elseif($next_level_key == 'city_id') {
			$name = $row['name'];

			$assigned_teachers = [];
			$centers = $model->getCenters($id);
			$requirement = 0;
			foreach ($centers as $center) {
				$assigned_teachers = array_merge($assigned_teachers, $model->getTeachers(['center_id' => $center['id']]));
				$batches = $model->getBatches($center['id']);
				foreach ($batches as $batch) {
					$requirement += findRequirementInBatch($batch['id']);
				}
			}
		}

		$data_row = array(
			'id'			=> $id,
			'name'			=> $name,
			'requirement'	=> $requirement,
		);
		if($next_level_key) {
			$data_row['buffer'] = ceil( $requirement * $buffer_percentage );
		}
		$data_row['teachers_assigned']	= count($assigned_teachers);

		$data[] = $data_row;
	}

	return $data;
}


function findRequirementInBatch($batch_id) {
	global $year, $sql;

	// Get all levels connected to this batch
	$levels = $sql->getAll("SELECT L.id, L.name, L.grade, L.medium, L.preferred_gender, COUNT(SL.student_id) AS student_count
							FROM Level L
							INNER JOIN BatchLevel BL ON BL.level_id=L.id
							INNER JOIN StudentLevel SL ON SL.level_id=L.id
							WHERE L.year='$year' AND BL.batch_id=$batch_id AND L.status='1' AND BL.year='$year'
							GROUP BY SL.level_id");

	$requirement = 0;
	foreach ($levels as $key => $level_row) {
		if(!$level_row['student_count']) continue;
		$teacher_count = 1;

		if($level_row['student_count'] > 5) $teacher_count = 2; // Two teachers if we have more than 5 students in class.

		$requirement += $teacher_count;
	}

	return $requirement;
}
