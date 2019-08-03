<?php
// NOT USED. Right now, the common model that is used is the apps/common/models/Common.php
// 
/// Common model. Includes all the neccessay data interactions. Not enough to make seperate models yet.
class Common {
	private $sql;

	function __construct() {
       global $sql;
       $this->sql = $sql;
	}

	public function getTeachers($source)
	{
		global $year;
		$where = "1=1";

		if(!empty($source['batch_id'])) {
			$where = "B.id = " . $source['batch_id'];

		} elseif(!empty($source['center_id'])) {
			$where = "C.id = " . $source['center_id'];

		} elseif(!empty($source['vertical_id'])) {
			return $this->sql->getAll("SELECT U.id,U.name,U.email,U.phone 
				FROM User U
				INNER JOIN UserGroup UG ON UG.user_id=U.id
				INNER JOIN `Group` G ON G.id=UG.group_id
				WHERE U.status='1' AND UG.year='$year' AND U.user_type='volunteer' AND G.vertical_id=$source[vertical_id]
				ORDER BY U.name");

		} elseif(!empty($source['city_id'])) {
			$where = "U.city_id = " . $source['city_id'];

		} else {
			return array();
		}

		$teachers = $this->sql->getAll("SELECT U.id,U.name,U.email,U.phone
			FROM User U
			INNER JOIN UserBatch UB ON UB.user_id=U.id
			INNER JOIN Batch B ON B.id=UB.batch_id
			INNER JOIN Center C ON C.id=B.center_id
			WHERE C.status='1' AND U.status='1' AND B.year='$year' AND U.user_type='volunteer' AND " . $where . "
			ORDER BY U.name");

		return $teachers;
	}

	public function getUsers($source) {
		global $year;
		$where = array("1=1");

		if(!empty($source['center_id']) or !empty($source['batch_id'])) {
			return $this->getTeachers($source);
		}

		if(!empty($source['city_id']) and $source['city_id']) $where[] = 'U.city_id=' . $source['city_id'];
		if(!empty($source['vertical_id'])) $where[] = 'G.vertical_id=' . $source['vertical_id'];
		if(!empty($source['group_id'])) $where[] = 'UG.group_id=' . $source['group_id'];
		if(!empty($source['group_type'])) $where[] = 'G.type="' . $source['group_type'] . '"';

		$query = "SELECT DISTINCT U.id,U.name,U.email,U.mad_email,U.phone 
				FROM User U
				INNER JOIN UserGroup UG ON UG.user_id=U.id
				INNER JOIN `Group` G ON G.id=UG.group_id
				WHERE U.status='1' AND UG.year='$year' AND U.user_type='volunteer' AND " . implode(' AND ', $where) . "
				ORDER BY U.name";

		return $this->sql->getAll($query);
	}

	/// :TODO: Very quickly thown together function. Make this better. Include groups, option to get non-'volunteer'.
	public function getUser($user_id)
	{
		global $year;

		return $this->sql->getAssoc("SELECT U.id,U.name,U.email,U.mad_email,U.phone,U.sex,
					U.photo,U.joined_on,U.address,U.birthday,U.left_on,U.reason_for_leaving,
					U.user_type,U.status,U.credit,U.city_id,
					C.name AS city, GROUP_CONCAT(DISTINCT G.name SEPARATOR ',')
				FROM User U 
				INNER JOIN City C ON U.city_id=C.id 
				LEFT JOIN UserGroup UG ON U.id=UG.user_id AND UG.year=$year
				LEFT JOIN `Group` G ON G.id=UG.group_id
				WHERE U.status='1' AND U.id=$user_id 
				GROUP BY UG.user_id");
	}

	public function getUserGroups($user_id)
	{
		global $year;
		return $this->sql->getAll("SELECT G.id, G.name, G.vertical_id, G.type 
				FROM `Group` G
				INNER JOIN UserGroup UG ON UG.group_id=G.id
				WHERE UG.year=$year AND UG.user_id=$user_id");
	}

	public function getCities()
	{
		return $this->sql->getAll("SELECT id,name FROM City WHERE type='actual' ORDER BY name");
	}
	
	public function getCityName($city_id)
	{
		return $this->sql->getOne("SELECT name FROM City WHERE id=$city_id");
	}

	public function getCenters($city_id)
	{
		return $this->sql->getAll("SELECT id,name FROM Center WHERE status='1' AND city_id=$city_id ORDER BY name");
	}
	public function getCenterName($center_id)
	{
		return $this->sql->getOne("SELECT name FROM Center WHERE id=$center_id");
	}


	public function getVerticals()
	{
		return $this->sql->getAll("SELECT id,name FROM Vertical WHERE status='1' ORDER BY name");
	}
	public function getVerticalName($vertical_id)
	{
		return $this->sql->getOne("SELECT name FROM Vertical WHERE id=$vertical_id");
	}

	public function getBatches($center_id)
	{
		global $year;

		return $this->sql->getAll("SELECT id,CONCAT((CASE day
										WHEN '0' THEN 'Sunday'
										WHEN '1' THEN 'Monday'
										WHEN '2' THEN 'Tuesday'
										WHEN '3' THEN 'Wednesday'
										WHEN '4' THEN 'Thursday'
										WHEN '5' THEN 'Friday'
										WHEN '6' THEN 'Saturday'
										ELSE ''
										END), ' ', TIME_FORMAT(class_time, '%l:%i %p')) AS name 
									FROM Batch 
									WHERE status='1' AND center_id=$center_id AND year=$year
									ORDER BY day");
	}

	public function getBatchName($batch_id)
	{
		return $this->sql->getOne("SELECT CONCAT(Center.name, ' : ', (CASE day
										WHEN '0' THEN 'Sunday'
										WHEN '1' THEN 'Monday'
										WHEN '2' THEN 'Tuesday'
										WHEN '3' THEN 'Wednesday'
										WHEN '4' THEN 'Thursday'
										WHEN '5' THEN 'Friday'
										WHEN '6' THEN 'Saturday'
										ELSE ''
										END), ' ', TIME_FORMAT(class_time, '%l:%i %p')) AS name 
									FROM Batch 
									INNER JOIN Center ON Center.id=Batch.center_id
									WHERE Batch.id=$batch_id");
	}

	public function getUserName($user_id) 
	{
		return $this->sql->getOne("SELECT name FROM User WHERE id=$user_id");
	}

	/// :DEPRICATED: :ALIAS:
	public function getTeacherName($user_id) { return $this->getUserName($user_id);	}

	public function getStudents($user_id) 
	{
		global $year;
		return $this->sql->getAll("SELECT S.id,S.name 
									FROM Student S
									INNER JOIN StudentLevel SL ON SL.student_id=S.id 
									INNER JOIN Level L ON L.id=SL.level_id
									INNER JOIN UserBatch UB ON UB.level_id=SL.level_id
									WHERE L.year = $year AND S.status='1' AND UB.user_id= $user_id
									ORDER BY S.name");
	}
}