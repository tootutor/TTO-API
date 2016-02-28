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
	protected function postNewItem($taskId, $seq, $itemTypeId, $code, $content) 
	{
  	if (\TTO::getRole() == 'admin') {
			$statement = '
				INSERT INTO item (taskId, seq, itemTypeId, code, content)
				VALUES (:taskId, :seq, :itemTypeId, :code, :content)
			';
			$bind = array(
        'taskId'     => $taskId,
        'seq'        => $seq,
        'itemTypeId' => $itemTypeId,
        'code'       => $code,
        'content'    => $content
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
      if ($taskId > 0) {
        $statement = 'SELECT * FROM item WHERE taskId = :taskId';
        $bind = array('taskId' => $taskId);
        return \Db::getResult($statement, $bind);
      } else {
        $statement = 'SELECT * FROM item';
        return \Db::getResult($statement);
      }
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
        SELECT I.*, UI.userId, UI.status, UI.point, UI.userContent
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
      return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url POST {itemId}/user/{userId}
   */ 
	protected function postUserItem($userId, $itemId, $point, $userContent) 
	{
    if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
			$statement = '
				INSERT INTO user_item (itemId, userId, point, userContent)
				VALUES (:itemId, :userId, :point, :userContent)
			';
			$bind = array(
        'itemId'      => $itemId,
        'userId'      => $userId,
        'point'       => $point,
        'userContent' => $userContent
      );
			\Db::execute($statement, $bind);
			return;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
  
  /**
   * @url PUT /{itemId}
   */ 
	protected function updateItem($itemId, $seq, $itemTypeId, $code, $content) 
	{
  	if (\TTO::getRole() == 'admin') {
			$statement = '
        UPDATE item
        SET
          seq        = :seq,
          itemTypeId = :itemTypeId,
          code       = :code,
          content    = :content
        WHERE itemId = :itemId
			';
			$bind = array(
        'itemId'     => $itemId,
        'seq'        => $seq,
        'itemTypeId' => $itemTypeId,
        'code'       => $code,
        'content'    => $content
      );
			\Db::execute($statement, $bind);
			return;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
	
}