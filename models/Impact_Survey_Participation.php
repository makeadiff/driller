<?php

class Impact_Survey_Participation {
	private $sql;

	function __construct() {
       global $sql;
       $this->sql = $sql;
	}

	public function getResponseCount($teacher_ids, $is_event_id)
	{
		if(!$teacher_ids) return array();

		$responses = $this->sql->getById("SELECT R.user_id,COUNT(R.id) AS responses 
			FROM IS_Response R
			WHERE R.is_event_id=$is_event_id AND R.user_id IN (" . implode(",", $teacher_ids) . ")
			GROUP BY R.user_id");

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
			GROUP BY SL.level_id");

		return $students;
	}

	public function getISResponses($is_event_id, $user_id, $student_id) 
	{
		$responses = $this->sql->getById("SELECT R.question_id, R.response
			FROM IS_Response R
			WHERE R.is_event_id=$is_event_id AND R.user_id=$user_id AND student_id=$student_id");

		return $responses;
	}

	public function getLatestISEvent()
	{
		return $this->sql->getAssoc("SELECT id,name,added_on FROM IS_Event ORDER BY added_on DESC LIMIT 0,1");
	}

	public function getISQuestions()
	{
		return $this->sql->getById("SELECT id,question FROM IS_Question WHERE status='1'");
	}
}