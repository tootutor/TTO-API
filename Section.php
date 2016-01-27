<?php
use Luracast\Restler\RestException;

class Section
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET course/{courseId}
   */ 
	protected function getAllSection($courseId) 
	{
  	$statement = 'SELECT * FROM section WHERE courseId = :courseId';
  	$bind = array('courseId' => $courseId);
		return \Db::getResult($statement, $bind);
	}

  /**
   * @url GET user/{userId}
   */ 
	protected function getAllUserSection($userId, $courseId) 
	{
  	$statement = '
      SELECT * 
        FROM section AS S
       WHERE S.courseId = :courseId
    ';
  	$bind = array('courseId' => $courseId);
		return \Db::getResult($statement, $bind);
	}
  
}

