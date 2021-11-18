<?php

class CPP_Agreement {
	private $sql;

	function __construct() {
       global $sql;
       $this->sql = $sql;
	}

	public function getAgreementStatus($user_ids)
	{
		if(!$user_ids) return array();

		$agreements = $this->sql->getById("SELECT user_id, data FROM UserData 
			WHERE name='child_protection_policy_signed' AND value='1' AND user_id IN (" . implode(',', $user_ids) . ")");

		return $agreements;
	}
}