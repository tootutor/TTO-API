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
	protected function postNewTask($code, $content, $taskTypeId, array $allItem) 
	{
  	if (\TTO::getRole() == 'admin') {
			$statement = '
				INSERT INTO task (code, content, taskTypeId)
				VALUES (:code, :content, :taskTypeId)
			';
			$bind = array('code' => $code, 'content' => $content, 'taskTypeId' => $taskTypeId);
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
   * @url GET allitem
   */ 
	protected function getAllItem() 
	{
  	$statement = 'SELECT * FROM item';
		return \Db::getResult($statement);
	}

  /**
   * @url GET allnewitem
   */ 
	protected function getNewAllItem() 
	{
  	$statement = '
  		SELECT * FROM item AS I 
  		WHERE NOT EXISTS (SELECT 1 FROM course_item AS CI
  		                  WHERE CI.itemId = I.itemId)
  	';
		return \Db::getResult($statement);
	}

  /**
   * @url GET user/{userId}
   */ 
	protected function getAllUserItem($userId, $sectionId) 
	{
    if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
      $statement = '
        SELECT I.*, IG.name AS itemGroup, IG.theme
        FROM item AS I
        INNER JOIN item_group AS IG
        ON IG.itemGroupId = I.itemGroupId
        WHERE I.sectionId = :sectionId
      ';
      $bind = array('sectionId' => $sectionId);
      return \Db::getResult($statement, $bind);
    } else {
      throw new RestException(401, 'No Authorize or Invalid request !!!');
    }
		return \Db::getResult($statement);
	}
  
  
  /**
   * @url GET item/{itemId}
   */ 
	protected function getItem($itemId) 
	{
		$statement = 'SELECT * FROM item WHERE itemId = :itemId';
		$bind = array('itemId' => $itemId);
		$response = \Db::getRow($statement, $bind);
		$response += array('allItemDetail' => array());

		$statement = 'SELECT * FROM item_detail WHERE itemId = :itemId ORDER BY seq';
		$bind = array('itemId' => $itemId);
		$allItemDetail = \Db::getResult($statement, $bind);

		foreach ($allItemDetail as $itemDetail) {
			if ($itemDetail['itemTypeId'] == 1) {
				$statement = 'SELECT * FROM item_radio WHERE itemDetailId = :itemDetailId';
				$bind = array('itemDetailId' => $itemDetail['itemDetailId']);
				$allItemRadio = \Db::getResult($statement, $bind);
				$itemDetail += array('allItemRadio' => $allItemRadio);
			}
			if ($itemDetail['itemTypeId'] == 3) {
				$statement = 'SELECT * FROM item_select WHERE itemDetailId = :itemDetailId';
				$bind = array('itemDetailId' => $itemDetail['itemDetailId']);
				$allItemSelect = \Db::getResult($statement, $bind);
				$itemDetail += array('allItemSelect' => $allItemSelect);
			}
			if ($itemDetail['itemTypeId'] == 4) {
				$statement = 'SELECT * FROM item_input WHERE itemDetailId = :itemDetailId';
				$bind = array('itemDetailId' => $itemDetail['itemDetailId']);
				$allItemInput = \Db::getResult($statement, $bind);
				$itemDetail += array('allItemInput' => $allItemInput);
			}
			$response['allItemDetail'][] = $itemDetail;
		}

		return $response;
	}

  /**
   * @url POST updateitem
   */ 
	protected function postUpdateItem($itemId, $code, $content, $itemGroupId) 
	{
  	if (\TTO::getRole() == 'admin') {
			$statement = '
				UPDATE item 
				SET code        = :code, 
				    content     = :content,
				    itemGroupId = :itemGroupId
				WHERE itemId = :itemId
			';
	  	$bind = array(
	  		'itemId'       => $itemId,
	  		'code'         => $code,
	  		'content'      => $content,
	  		'itemGroupId'  => $itemGroupId
	  	);
			$row_update = \Db::execute($statement, $bind);

	  	$response = new \stdClass();
			$response->row_update = $row_update;
			return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET allitemtype
   */ 
	protected function getAllItemType() 
	{
  	$statement = 'SELECT * FROM item_type';
		$response = \Db::getResult($statement);
		
		return $response;
	}

  /**
   * @url GET getitemdetail/{itemDetailId}
   */ 
	protected function getItemDetail($itemDetailId) 
	{
  	$statement = 'SELECT * FROM item_detail WHERE itemDetailId = :itemDetailId';
  	$bind = array('itemDetailId' => $itemDetailId);
		$response = \Db::getResult($statement, $bind);
		
		return $response;
	}

  /**
   * @url POST newitemdetail
   */ 
	protected function postNewItemDetail($itemId, $seq, $itemTypeId, $code, $content, $isAction, $showOption) 
	{
  	if (\TTO::getRole() == 'admin') {
			$statement = '
				INSERT INTO item_detail (itemId, seq, itemTypeId, code, content, isAction, showOption)
				VALUES (:itemId, :seq, :itemTypeId, :code, :content, :isAction, :showOption)
			';
	  	$bind = array(
	  		'itemId'      => $itemId,
	  		'seq'         => $seq,
	  		'itemTypeId'  => $itemTypeId,
	  		'code'        => $code,
	  		'content'     => $content,
	  		'isAction'    => $isAction,
	  		'showOption'  => $showOption
	  	);
			$row_insert = \Db::execute($statement, $bind);
			$itemDetailId = \Db::getLastInsertId();

	  	$response = new \stdClass();
			$response->itemDetailId = $itemDetailId;
			return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url POST updateitemdetail
   */ 
	protected function postUpdateItemDetail($itemDetailId, $itemId, $seq, $itemTypeId, $code, $isAction, $showOption, $content="") 
	{
  	if (\TTO::getRole() == 'admin') {
			$statement = '
				UPDATE item_detail 
				SET itemId     = :itemId, 
				    seq        = :seq, 
				    itemTypeId = :itemTypeId,
				    code       = :code, 
				    content    = :content, 
				    isAction   = :isAction,
				    showOption = :showOption
				WHERE itemDetailId = :itemDetailId
			';
	  	$bind = array(
	  		'itemDetailId' => $itemDetailId,
	  		'itemId'       => $itemId,
	  		'seq'          => $seq,
	  		'itemTypeId'   => $itemTypeId,
	  		'code'         => $code,
	  		'content'      => $content,
	  		'isAction'     => $isAction,
	  		'showOption'   => $showOption
	  	);
			$row_update = \Db::execute($statement, $bind);

	  	$response = new \stdClass();
			$response->row_update = $row_update;
			return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}

	}

  /**
   * @url POST updateitemradio
   */ 
	protected function postUpdateItemRadio($itemRadioId, $content, $isAnswer, $point) 
	{
  	if (\TTO::getRole() == 'admin') {
			$statement = '
				UPDATE item_radio 
				SET content    = :content, 
				    isAnswer   = :isAnswer,
				    point      = :point
				WHERE itemRadioId = :itemRadioId
			';
	  	$bind = array(
	  		'itemRadioId' => $itemRadioId,
	  		'content'     => $content,
	  		'isAnswer'    => $isAnswer,
	  		'point'       => $point
	  	);
			$row_update = \Db::execute($statement, $bind);

	  	$response = new \stdClass();
			$response->row_update = $row_update;
			return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}

	}

  /**
   * @url POST updateitemselect
   */ 
	protected function postUpdateItemSelect($itemSelectId, $content, $isAnswer, $point) 
	{
  	if (\TTO::getRole() == 'admin') {
			$statement = '
				UPDATE item_select 
				SET content    = :content, 
				    isAnswer   = :isAnswer,
				    point      = :point
				WHERE itemSelectId = :itemSelectId
			';
	  	$bind = array(
	  		'itemSelectId' => $itemSelectId,
	  		'content'      => $content,
	  		'isAnswer'     => $isAnswer,
	  		'point'        => $point
	  	);
			$row_update = \Db::execute($statement, $bind);

	  	$response = new \stdClass();
			$response->row_update = $row_update;
			return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
	
  /**
   * @url POST updateiteminput
   */ 
	protected function postUpdateItemInput($itemInputId, $question, $answer, $answerType, $point) 
	{
  	if (\TTO::getRole() == 'admin') {
			$statement = '
				UPDATE item_input
				SET question   = :question, 
				    answer     = :answer,
				    answerType = :answerType,
				    point      = :point
				WHERE itemInputId = :itemInputId
			';
	  	$bind = array(
	  		'itemInputId' => $itemInputId,
	  		'question'    => $question,
	  		'answer'      => $answer,
	  		'answerType'  => $answerType,
	  		'point'       => $point
	  	);
			$row_update = \Db::execute($statement, $bind);

	  	$response = new \stdClass();
			$response->row_update = $row_update;
			return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}

	}

  /**
   * @url GET allitemgroup
   */ 
	protected function getAllItemGroup() 
	{
  	$statement = 'SELECT * FROM item_group';
		$response = \Db::getResult($statement);
		
		return $response;
	}

	
}