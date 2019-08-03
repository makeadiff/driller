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

		$donuts = $this->sql->getById("SELECT U.id, COALESCE(COUNT(D.id), 0) AS count , COALESCE(SUM(D.amount),0) AS total
			FROM User U
			INNER JOIN Donut_Donation D ON D.fundraiser_user_id=U.id
			WHERE U.id IN (" . implode(",", $user_ids) . ") AND D.added_on > '$year-06-01 00:00:00' AND U.city_id!=28
			GROUP BY U.id");

		return $donuts;
	}
}