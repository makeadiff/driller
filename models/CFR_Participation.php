<?php

class CFR_Participation {
	private $sql;

	function __construct() {
       global $sql;
       $this->sql = $sql;
	}

	public function getDonations($user_ids)
	{
		if(!$user_ids) return array();
		global $year, $config;

		$donut_sql = new Sql($config['db_host'], $config['db_user'], $config['db_password'], 'makeadiff_cfrapp');
		$donuts = $donut_sql->getById("SELECT U.madapp_user_id AS id, COALESCE(COUNT(D.id),0) AS count, COALESCE(SUM(D.donation_amount),0) AS total
			FROM users U 
			INNER JOIN donations D ON D.fundraiser_id=U.id
			WHERE U.madapp_user_id IN (" . implode(",", $user_ids) . ") AND U.is_deleted=0 AND D.created_at > '2017-08-01 00:00:00'
			GROUP BY D.fundraiser_id");

		$externals = $donut_sql->getById("SELECT U.madapp_user_id AS id, COALESCE(COUNT(D.id),0) AS count, COALESCE(SUM(D.amount),0) AS total
			FROM users U 
			INNER JOIN external_donations D ON D.fundraiser_id=U.id
			WHERE U.madapp_user_id IN (" . implode(",", $user_ids) . ") AND U.is_deleted=0 AND D.created_at > '2017-08-01 00:00:00'
			GROUP BY D.fundraiser_id");

		foreach ($externals as $user_id => $row) {
			if(!empty($donuts[$user_id])) {
				$donuts[$user_id]['count'] += $row['count'];
				$donuts[$user_id]['total'] += $row['total'];
			} else {
				$donuts[$user_id]['count'] = $row['count'];
				$donuts[$user_id]['total'] = $row['total'];
			}

		}

		return $donuts;
	}
}