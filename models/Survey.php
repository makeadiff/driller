<?php
class Survey {
	private $sql;

	function __construct() {
       global $sql;
       $this->sql = $sql;
	}

	public function getResponseData($survey_id, $responder_ids)
	{
		if(!$responder_ids) return array();
		global $config;

		$responses = $this->sql->getById("SELECT responder_id, AVG(response) AS average_response, COUNT(id) AS response_count FROM Survey_Response 
			WHERE survey_id = $survey_id AND responder_id IN (" . implode(',', $responder_ids) . ")
			GROUP BY responder_id");

		return $responses;
	}

	public function getSurvey($survey_id) {
		return $this->sql->getAssoc("SELECT S.id,S.name,T.id AS template_id,T.name AS template_name,T.description,T.vertical_id,T.responder,T.options FROM Survey S 
				INNER JOIN Survey_Template T ON T.id=S.survey_template_id WHERE S.id = $survey_id AND S.status='1' AND T.status='1'");
	}

	public function getTemplate($survey_template_id) {
		return $this->sql->getAssoc("SELECT id,name,vertical_id,responder,options FROM Survey_Template WHERE id = $survey_template_id AND status='1'");
	}

	public function getQuestions($survey_template_id) {
		return $this->sql->getById("SELECT id,question FROM Survey_Question WHERE survey_template_id=$survey_template_id AND status='1' ORDER BY sort_order");
	}

	public function getResponseCount($survey_id, $responder_ids)
	{
		if(!$responder_ids) return array();

		$responses = $this->sql->getById("SELECT responder_id,COUNT(id) AS responses 
			FROM Survey_Response R
			WHERE survey_id=$survey_id AND responder_id IN (" . implode(",", $responder_ids) . ")
			GROUP BY responder_id");

		return $responses;
	}

	public function getStudentCount($teacher_ids)
	{
		if(!$teacher_ids) return array();
		global $year;

		// NOTE : Still not sure if it should be COUNT(DISTINCT SL.student_id) or not. Without distinct it will give the necessary value. If two teachers are teaching the same student, it will be counted as 2.
		$students = $this->sql->getById("SELECT UB.user_id, COUNT(SL.student_id) AS student_count
			FROM Student S
			INNER JOIN StudentLevel SL ON SL.student_id=S.id 
			INNER JOIN Level L ON L.id=SL.level_id
			INNER JOIN UserBatch UB ON UB.level_id=SL.level_id
			WHERE L.year = $year AND S.status='1' AND UB.user_id IN (" . implode(",", $teacher_ids) . ")
			GROUP BY UB.user_id");

		return $students;
	}

	public function getResponses($survey_id, $responder_id) 
	{
		$responses = $this->sql->getById("SELECT survey_question_id, response, survey_choice_id
			FROM Survey_Response R
			WHERE survey_id=$survey_id AND responder_id=$responder_id");
		return $responses;
	}

}