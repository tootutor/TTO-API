<?php
use Luracast\Restler\RestException;

class Item
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url POST
   */ 
	protected function postNewItem($taskId, $userId, $status, $point) 
	{
  	if (\TTO::getRole() == 'admin') {
			$statement = '
				INSERT INTO item (taskId, userId, status, point)
				VALUES (:taskId, :userId, :status, :point)
			';
			$bind = array(
        'taskId' => $taskId,
        'userId' => $userId,
        'status' => $status,
        'point'  => $point
      );
			\Db::execute($statement, $bind);
			return;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET
   */ 
	protected function getAllItem($taskId) 
	{
  	if (\TTO::getRole() == 'admin') {
      $statement = 'SELECT * FROM item WHERE taskId = :taskId';
			$bind = array('taskId' => $taskId);
      return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET /user/{userId}
   */ 
	protected function getAllUserItem($userId, $taskId) 
	{
    if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
      $statement = '
        SELECT I.*, UI.userId, UI.status, UI.point
        FROM item AS I
        LEFT OUTER JOIN user_item AS UI
        ON UI.itemId = I.itemId
        AND UI.userId = :userId
        WHERE I.taskId = :taskId
      ';
			$bind = array(
        'userId' => $userId,
        'taskId' => $taskId
      );
      return \Db::getResult($statement);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
	
}