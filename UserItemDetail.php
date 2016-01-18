<?php
use Luracast\Restler\RestException;

class UserItemDetail
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET by-course-item/{userId}/{courseItemId}
   */ 
	protected function getByCourseItem($userId, $courseItemId) 
	{
      	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
    	  	$statement = '
    			SELECT UCI.*, CI.*, I.code, I.content, I.itemGroupId
    			FROM course_item AS CI
    			INNER JOIN item AS I
    			ON CI.itemId = I.itemId
    			LEFT OUTER JOIN user_course_item AS UCI
    			ON UCI.courseItemId = CI.courseItemId
    			AND UCI.userId = :userId
    			WHERE CI.courseItemId = :courseItemId
    	  	';
    	  	$bind = array('courseItemId' => $courseItemId, 'userId' => $userId);
			$response = \Db::getRow($statement, $bind);
			$itemId = $response['itemId'];
			$userCourseItemId = $response['userCourseItemId'];
			$response += array('allItemDetail' => array());
	
			$statement = '
                SELECT UCID.*, ID.*
                FROM item_detail AS ID
                LEFT OUTER JOIN user_course_item_detail AS UCID
                ON UCID.itemDetailId = ID.itemDetailId
                AND UCID.userCourseItemId = :userCourseItemId
                WHERE itemId = :itemId 
                ORDER BY seq
			';
			$bind = array('itemId' => $itemId, 'userCourseItemId' => $userCourseItemId);
			$allItemDetail = \Db::getResult($statement, $bind);
	
			foreach ($allItemDetail as $itemDetail) {
				if ($itemDetail['itemTypeId'] == 1) {
					if ($itemDetail['status'] == 'done') {
						$statement = '
							SELECT itemRadioId FROM user_course_item_radio 
							 WHERE userCourseItemId = :userCourseItemId
							   AND itemDetailId     = :itemDetailId
						';
						$bind = array(
							'userCourseItemId' => $userCourseItemId,
							'itemDetailId'     => $itemDetail['itemDetailId']
						);
						$userItemRadioId = \Db::getValue($statement, $bind);
						$itemDetail += array('userItemRadioId' => $userItemRadioId);
					}
					$statement = 'SELECT * FROM item_radio WHERE itemDetailId = :itemDetailId';
					$bind = array('itemDetailId' => $itemDetail['itemDetailId']);
					$allItemRadio = \Db::getResult($statement, $bind);
					$itemDetail += array('allItemRadio' => $allItemRadio);
				}
				
				if ($itemDetail['itemTypeId'] == 3) {
					$statement = '
						SELECT IDS.*, UCIS.userIsAnswer
						  FROM item_select AS IDS
						LEFT OUTER JOIN user_course_item_select AS UCIS
						    ON IDS.itemSelectId = UCIS.itemSelectId
						   AND IDS.itemDetailId = UCIS.itemDetailId
						   AND UCIS.userCourseItemId = :userCourseItemId
						 WHERE IDS.itemDetailId = :itemDetailId
					';
					$bind = array(
						'itemDetailId'     => $itemDetail['itemDetailId'],
						'userCourseItemId' => $userCourseItemId
					);
					$allItemSelect = \Db::getResult($statement, $bind);
					$itemDetail += array('allItemSelect' => $allItemSelect);
				}

				if ($itemDetail['itemTypeId'] == 4) {
					$statement = '
						SELECT II.*, UCII.userAnswer
						  FROM item_input AS II
						LEFT OUTER JOIN user_course_item_input AS UCII
						    ON II.itemInputId  = UCII.itemInputId
						   AND II.itemDetailId = UCII.itemDetailId
						   AND UCII.userCourseItemId = :userCourseItemId
						 WHERE II.itemDetailId = :itemDetailId
					';
					$bind = array(
						'itemDetailId'     => $itemDetail['itemDetailId'],
						'userCourseItemId' => $userCourseItemId
					);
					$allItemInput = \Db::getResult($statement, $bind);
					$itemDetail += array('allItemInput' => $allItemInput);
				}

				$response['allItemDetail'][] = $itemDetail;
			}
			return $response;
      	} else {
      		throw new RestException(401, 'No Authorize or Invalid request !!!');
      	}
	}

  /**
   * @url POST add-item-radio
   */ 
	protected function postAddItemRadio($userId, $userCourseItemId, $itemDetailId, $itemRadioId, $point, $actionCount) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
		
		// Add new user item radio type
		$statement = '
			INSERT INTO user_course_item_radio (userCourseItemId, itemDetailId, itemRadioId)
			VALUES (:userCourseItemId, :itemDetailId, :itemRadioId)
		';
		$bind = array(
			'userCourseItemId' => $userCourseItemId, 
			'itemDetailId'     => $itemDetailId, 
			'itemRadioId'      => $itemRadioId
		);
		\Db::execute($statement, $bind);
		
		// Update item detail status
		$statement = '
			UPDATE user_course_item_detail
			   SET point  = :point,
			       status = :status
			 WHERE userCourseItemId = :userCourseItemId
			   AND itemDetailId     = :itemDetailId
		';
		$bind = array(
			'userCourseItemId' => $userCourseItemId, 
			'itemDetailId'     => $itemDetailId, 
			'status'           => 'done',
			'point'            => $point
		);
		\Db::execute($statement, $bind);
		
		// Update number of remaining action item
		if ($actionCount > 0) {	$status = 'start'; } else {	$status = 'done'; }
		$statement = '
			UPDATE user_course_item
			   SET actionCount = :actionCount,
			       point       = point + :point,
			       status      = :status
			 WHERE userCourseItemId = :userCourseItemId
		';
		$bind = array(
			'userCourseItemId' => $userCourseItemId,
			'actionCount'      => $actionCount,
			'point'            => $point,
			'status'           => $status
		);
		\Db::execute($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url POST add-item-select
   */ 
	protected function postAddItemSelect($userId, $userCourseItemId, $itemDetailId, $point, $actionCount, array $allItemSelect) 
	{
      	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
    			
    		// Add new user item radio type
    		foreach ($allItemSelect as $itemSelect) {
    			$statement = '
    				INSERT INTO user_course_item_select (userCourseItemId, itemDetailId, itemSelectId, userIsAnswer)
    				VALUES (:userCourseItemId, :itemDetailId, :itemSelectId, :userIsAnswer)
    			';
    			$bind = array(
    				'userCourseItemId' => $userCourseItemId, 
    				'itemDetailId'     => $itemDetailId, 
    				'itemSelectId'     => $itemSelect['itemSelectId'],
    				'userIsAnswer'     => 1
    			);
    			\Db::execute($statement, $bind);
    		}
    		
    		// Update item detail status
    		$statement = '
    			UPDATE user_course_item_detail
    			   SET point  = :point,
    			       status = :status
    			 WHERE userCourseItemId = :userCourseItemId
    			   AND itemDetailId     = :itemDetailId
    		';
    		$bind = array(
    			'userCourseItemId' => $userCourseItemId, 
    			'itemDetailId'     => $itemDetailId, 
    			'status'           => 'done',
    			'point'            => $point
    		);
    		\Db::execute($statement, $bind);
    		
    		// Update number of remaining action item
    		if ($actionCount > 0) {	$status = 'start'; } else {	$status = 'done'; }
    		$statement = '
    			UPDATE user_course_item
    			   SET actionCount = :actionCount,
    			       point       = point + :point,
    			       status      = :status
    			 WHERE userCourseItemId = :userCourseItemId
    		';
    		$bind = array(
    			'userCourseItemId' => $userCourseItemId,
    			'actionCount'      => $actionCount,
    			'point'            => $point,
    			'status'           => $status
    		);
    		\Db::execute($statement, $bind);
      	} else {
      		throw new RestException(401, 'No Authorize or Invalid request !!!');
      	}
	}

  /**
   * @url POST add-item-input
   */ 
	protected function postAddItemInput($userId, $userCourseItemId, $itemDetailId, $point, $actionCount, array $allItemInput) 
	{
      	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
    			
			// Add new user item input type
			foreach ($allItemInput as $itemInput) {
				$statement = '
					INSERT INTO user_course_item_input (userCourseItemId, itemDetailId, itemInputId, userAnswer)
					VALUES (:userCourseItemId, :itemDetailId, :itemInputId, :userAnswer)
				';
				$bind = array(
					'userCourseItemId' => $userCourseItemId, 
					'itemDetailId'     => $itemDetailId, 
					'itemInputId'      => $itemInput['itemInputId'],
					'userAnswer'       => $itemInput['userAnswer']
				);
				\Db::execute($statement, $bind);
			}
			
			// Update item detail status
			$statement = '
				UPDATE user_course_item_detail
				   SET point  = :point,
				       status = :status
				 WHERE userCourseItemId = :userCourseItemId
				   AND itemDetailId     = :itemDetailId
			';
			$bind = array(
				'userCourseItemId' => $userCourseItemId, 
				'itemDetailId'     => $itemDetailId, 
				'status'           => 'done',
				'point'            => $point
			);
			\Db::execute($statement, $bind);
			
			// Update number of remaining action item
			if ($actionCount > 0) {	$status = 'start'; } else {	$status = 'done'; }
			$statement = '
				UPDATE user_course_item
				   SET actionCount = :actionCount,
				       point       = point + :point,
				       status      = :status
				 WHERE userCourseItemId = :userCourseItemId
			';
			$bind = array(
				'userCourseItemId' => $userCourseItemId,
				'actionCount'      => $actionCount,
				'point'            => $point,
				'status'           => $status
			);
			\Db::execute($statement, $bind);
      	} else {
      		throw new RestException(401, 'No Authorize or Invalid request !!!');
      	}
	}

}


