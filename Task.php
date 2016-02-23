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
   * @url GET {taskId}
   */ 
	protected function getTask($taskId) 
	{
  	if (\TTO::getRole() == 'admin') {
      $statement = '
        SELECT T.*, TT.name AS taskTypeName, TT.theme 
        FROM task AS T
        INNER JOIN task_type AS TT
        ON TT.taskTypeId = T.taskTypeId
        WHERE taskId = :taskId
      ';
			$bind = array('taskId' => $taskId);
      return \Db::getRow($statement, $bind);
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

  /**
   * @url GET {taskId}/user/{userId}
   */
	protected function getUserTask($userId, $taskId) 
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
        WHERE taskId = :taskId
      ';
			$bind = array('userId' => $userId, 'taskId' => $taskId);
      return \Db::getRow($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url PUT {taskId}
   * @url PUT {taskId}/user
   * @url PUT {taskId}/user/{userId}
   */ 
  protected function updateTask($taskId, $code, $content, $seq, $taskTypeId, $userId = null) 
  {
    if (\TTO::getRole() == 'admin') {
      $statement = '
        UPDATE task
        SET 
          code        = :code,
          content     = :content,
          seq         = :seq,
          taskTypeId  = :taskTypeId
        WHERE
          taskId      = :taskId
      ';
      $bind = array(
        'taskId'      => $taskId,
        'code'        => $code,
        'content'     => $content,
        'seq'         => $seq,
        'taskTypeId'  => $taskTypeId,
      );
      \Db::execute($statement, $bind);
      return;
    } else {
      throw new RestException(401, 'No Authorize or Invalid request !!!');
    }
  }

  /**
   * @url POST
   * @url POST user/{userId}
   */ 
  protected function addTask($sectionId, $code, $content, $seq, $taskTypeId, $userId = null) 
  {
    if (\TTO::getRole() == 'admin') {
      $statement = '
        INSERT INTO task (sectionId, code, content, seq, taskTypeId)
        VALUES (:sectionId, :code, :content, :seq, :taskTypeId)
      ';
      $bind = array(
        'sectionId'  => $sectionId,
        'code'       => $code,
        'content'    => $content,
        'seq'        => $seq,
        'taskTypeId' => $taskTypeId
      );
      \Db::execute($statement, $bind);
      $taskId = \Db::getLastInsertId();

      $statement = 'SELECT * FROM task WHERE taskId = :taskId';
      $bind = array('taskId' => $taskId);
      return \Db::getRow($statement, $bind);
      
    } else {
      throw new RestException(401, 'No Authorize or Invalid request !!!');
    }
  }
	
}