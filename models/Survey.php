<?php
class Survey {
	private $sql;

	function __construct() {
       global $sql;
       $this->sql = $sql;
	}

	public function getResponses($survey_id, $responder_ids)
	{
		if(!$responder_ids) return array();
		global $config;

		$responses = $this->sql->getById("SELECT responder_id, AVG(response) AS average_response, COUNT(id) AS response_count FROM Survey_Response 
			WHERE survey_id = $survey_id AND responder_id IN (" . implode(',', $responder_ids) . ")
			GROUP BY responder_id");

		return $responses;
	}
}