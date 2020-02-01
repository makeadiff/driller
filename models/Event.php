<?php
class Event {
	private $sql;
	public $starts_on 	= '2018-06-01 00:00:00';
	public $ends_on	= '';

	function __construct() {
       global $sql;
       $this->sql = $sql;

       $this->starts_on = date(get_year() . '-05-01 00:00:00');
       $this->ends_on = date('Y-m-d H:i:s');
	}

	public function getEventType($event_type_id)
	{
		return $this->sql->getOne("SELECT name FROM Event_Type WHERE id=$event_type_id");
	}

	public function getEvent($event_id)
	{
		return $this->sql->getAssoc("SELECT id,name,description,place FROM Event WHERE id=$event_id");
	}

	public function getCollectiveStatus($event_id, $user_ids)
	{
		return $this->sql->getAll("SELECT UE.user_id AS id, UE.event_id, UE.user_choice, UE.present, E.starts_on
									FROM UserEvent UE 
									INNER JOIN Event E ON E.id=UE.event_id
									WHERE E.status='1' AND E.starts_on >= '$this->starts_on' AND (E.ends_on <= '$this->ends_on' OR E.ends_on IS NULL)
										AND E.id=$event_id AND UE.user_id IN (" . implode(",", $user_ids) . ")");
	}
}