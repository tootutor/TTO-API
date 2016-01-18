<?php
use Luracast\Restler\RestException;

class Category
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET
   */ 
	protected function getAllCategory() 
	{
  	$statement = 'SELECT * FROM category WHERE status = :status';
  	$bind = array('status' => 'active');
		return \Db::getResult($statement, $bind);
	}

	/**
	* @url GET user/{userId}
	*/ 
	protected function getUserCategory($userId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
				SELECT *
				FROM category AS CA
				WHERE EXISTS (
					SELECT 1
					FROM user_course AS UC
					INNER JOIN course AS C
					ON UC.userId = :userId
					AND UC.courseId = C.courseId
					WHERE C.categoryId = CA.categoryId
				)
			';
	  	$bind = array('userId' => $userId);
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

}

