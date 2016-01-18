<?php
use Luracast\Restler\RestException;

class UserCourse
{
  /**
   * @smart-auto-routing false
   */ 

  /**
   * @url GET availablecourse/{userId}
   */ 
	protected function getAvailableCourse($userId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT C.* 
	  		  FROM view_course_summary AS C
	  		 WHERE C.status = :status
	  		   AND NOT EXISTS (SELECT 1
	  		                     FROM user_course AS UC
	  		                    WHERE UC.courseId = C.courseId
	  		                      AND UC.userId = :userId
	  		                  )
	  	';
	  	$bind = array('status' => 'active', 'userId' => $userId);
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET allusercourse
   */ 
	protected function getAllUserCourse() 
	{
  	if (\TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT UC.*, C.*, U.nickname
	  		  FROM user_course AS UC
	  		 INNER JOIN course AS C
	  	      ON UC.courseId = C.courseId
	  	   INNER JOIN user AS U
	  	      ON UC.userId = U.userId
	  	';
			return \Db::getResult($statement);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET usercourselist/{userId}
   */ 
	protected function getUserCourseList($userId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT * 
	  		  FROM user_course AS UC
	  		 INNER JOIN  course AS C
	  	      ON UC.courseId = C.courseId
	  		 WHERE UC.userId = :userId 
	  	';
	  	$bind = array('userId' => $userId);
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url POST addusercourse/{userId}
   */ 
	protected function postAddUserCourse($userId, $courseId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = 'SELECT coin FROM user WHERE userId = :userId';
	  	$bind = array('userId' => $userId);
			$userCoin = \Db::getValue($statement, $bind);
  		
	  	$statement = 'SELECT coin FROM course WHERE courseId = :courseId';
	  	$bind = array('courseId' => $courseId);
			$courseCoin = \Db::getValue($statement, $bind);
			
			if ($userCoin < $courseCoin) {
				throw new RestException(500, 'Coin is not enough !!!');
			}

	  	$statement = '
	  		INSERT INTO user_course (userId, courseId, coin)
	  		VALUES (:userId, :courseId, :courseCoin)
	  	';
	  	$bind = array(
	  		'userId'     => $userId, 
	  		'courseId'   => $courseId, 
	  		'courseCoin' => $courseCoin
	  	);
	  	
	  	\TTOMail::createAndSendAdmin('A user adding a course', json_encode($bind));
	  	
			$row_insert = \Db::execute($statement, $bind);
			if ($row_insert > 0) { 
		  	$statement = 'UPDATE user SET coin = coin - :courseCoin WHERE userId = :userId';
		  	$bind = array('userId' => $userId, 'courseCoin' => $courseCoin);
				$row_update = \Db::execute($statement, $bind);
				if ($row_update > 0) {
			  	$response = new \stdClass();
			  	$response->row_insert = $row_insert;
			  	$response->row_update = $row_update;
					return $response;
				}
			} else {
	  		throw new RestException(500, 'Add a new course error !!!');
			}
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
	
}