<?php
use Luracast\Restler\RestException;

class Task
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET
   */ 
	protected function getAllTask($sectionId) 
	{
  	if (\TTO::getRole() == 'admin') {
      $statement = '
        SELECT T.*, TT.name AS taskTypeName, TT.theme 
        FROM task AS T
        INNER JOIN task_type AS TT
        ON TT.taskTypeId = T.taskTypeId
        WHERE sectionId = :sectionId
      ';
			$bind = array('sectionId' => $sectionId);
      return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET user/{userId}
   */ 
	protected function getAllUserTask($userId, $sectionId) 
	{
    if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
      $statement = '
        SELECT T.*, TT.name AS taskTypeName, TT.theme 
        FROM task AS T
        INNER JOIN task_type AS TT
        ON TT.taskTypeId = T.taskTypeId
        LEFT OUTER JOIN user_task AS UT
        ON UT.taskId = T.taskId
        AND UT.userId = :userId
        WHERE sectionId = :sectionId
      ';
			$bind = array('userId' => $userId, 'sectionId' => $sectionId);
      return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
	
}