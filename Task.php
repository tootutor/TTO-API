<?php
use Luracast\Restler\RestException;

class Task
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url POST
   */ 
	protected function postNewTask($code, $content, $rank, $taskTypeId, $sectionId) 
	{
  	if (\TTO::getRole() == 'admin') {
			$statement = '
				INSERT INTO task (code, content, rank, taskTypeId, sectionId)
				VALUES (:code, :content, :taskTypeId)
			';
			$bind = array(
        'code' => $code, 
        'content' => $content, 
        'rank' => $rank, 
        'taskTypeId' => $taskTypeId
        'sectionId' => $sectionId
      );
			$row_insert = \Db::execute($statement, $bind);
			$taskId = \Db::getLastInsertId();

	  	$response = new \stdClass();
			$response->taskId = $taskId;
			return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET
   */ 
	protected function getAllTask($sectionId) 
	{
  	if (\TTO::getRole() == 'admin') {
      $statement = 'SELECT * FROM task WHERE sectionId = :sectionId';
			$bind = array('sectionId' => $sectionId);
      
      return \Db::getResult($statement);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET /user/{userId}
   */ 
	protected function getAllUserTask($userId, $sectionId) 
	{
    if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
      $statement = '
        SELECT T.*, UT.userId, UT.status, UT.point
        FROM task AS T
        LEFT OUTER JOIN user_task AS UT
        ON UT.taskId = T.taskId
        AND UT.userId = :userId
        WHERE T.sectionId = :sectionId
      ';
			$bind = array(
        'userId' => $userId
        'sectionId' => $sectionId
      );
      return \Db::getResult($statement);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
  
  /**
   * @url PUT /{taskId}
   * @url PUT /{taskId}/user/{userId}
   */ 
	protected function putUpdateTask($taskId, $code, $content, $rank, $taskTypeId, $sectionId) 
	{
  	if (\TTO::getRole() == 'admin') {
			$statement = '
				UPDATE task 
				SET code        = :code, 
				    content     = :content,
				    rank        = :rank,
				    itemTypeId  = :itemTypeId,
				    sectionId   = :sectionId
				WHERE taskId = :taskId
			';
	  	$bind = array(
	  		'taskId'       => $taskId,
	  		'code'         => $code,
	  		'content'      => $content,
	  		'rank'         => $content,
	  		'itemTypeId'   => $itemTypeId,
	  		'sectionId'    => $sectionId
	  	);
			\Db::execute($statement, $bind);
			return;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
	
}