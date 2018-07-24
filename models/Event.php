<?php
class Event {
	private $sql;
	public $start_date = '2018-06-01 00:00:00';

	function __construct() {
       global $sql;
       $this->sql = $sql;
	}

	public function getEventType($event_type_id)
	{
		return $this->sql->getOne("SELECT name FROM Event_Type WHERE id=$event_type_id");
	}

	public function getCollectiveStatus($event_type_id, $user_ids, $present = false)
	{
		$attended = '';
		if($present !== false) $attended = "AND present='$present'";
		return $this->sql->getAll("SELECT UE.user_id AS id, UE.event_id, UE.present,E.starts_on
									FROM UserEvent UE 
									INNER JOIN Event E ON E.id=UE.event_id
									WHERE E.status='1' AND E.starts_on > '$this->start_date' AND E.event_type_id=$event_type_id AND UE.user_id IN (" . implode(",", $user_ids) . ") $attended");
	}
}