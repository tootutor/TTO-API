<?php
use Luracast\Restler\RestException;

class UserCourseItem
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET newitemlist/{userId}/{userCourseId}
   */ 
	protected function getNewItemList($userId, $userCourseId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT * 
	  		  FROM course_item AS CI
	  		 INNER JOIN   item AS I
	  		    ON I.itemId = CI.itemId 
	  		 INNER JOIN user_course AS UC
	  		    ON UC.courseId = CI.courseId
	  		 WHERE UC.userCourseId = :userCourseId
	  		   AND NOT EXISTS (
	  		       SELECT 1
	  		         FROM user_course_item AS UCI
	  		        WHERE UCI.courseItemId = CI.courseItemId
	  		          AND UCI.userCourseId = :userCourseId
	  		   )
	  		 ORDER BY I.rank DESC
	  	';
	  	$bind = array('userCourseId' => $userCourseId);
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET startitemlist/{userId}/{userCourseId}
   */ 
	protected function getStartItemList($userId, $userCourseId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT * 
	  		FROM user_course_item AS UCI
	  		INNER JOIN course_item AS CI
	  		   ON UCI.courseItemId = CI.courseItemId
	  		  AND UCI.userId = :userId
	  		INNER JOIN item AS I
	  		   ON CI.itemId = I.itemId
	  	  INNER JOIN user_course AS UC
	         ON UC.courseId = CI.courseId
					AND UC.userId = :userId
	  		WHERE UCI.userCourseId = :userCourseId
	  		  AND UCI.status = :status
	  		ORDER BY UCI.userCourseItemId DESC
	  	';
	  	$bind = array('userId' => $userId, 'userCourseId' => $userCourseId, 'status' => 'start');
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET doneitemlist/{userId}/{userCourseId}
   */ 
	protected function getDoneItemList($userId, $userCourseId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT * 
	  		FROM user_course_item AS UCI
	  		INNER JOIN course_item AS CI
	  		   ON UCI.courseItemId = CI.courseItemId
	  		  AND UCI.userId = :userId
	  		INNER JOIN item AS I
	  		   ON CI.itemId = I.itemId
	  	  INNER JOIN user_course AS UC
	         ON UC.courseId = CI.courseId
					AND UC.userId = :userId
	  		WHERE UCI.userCourseId = :userCourseId
	  		  AND UCI.status = :status
	  		ORDER BY UCI.userCourseItemId DESC
	  	';
	  	$bind = array('userId' => $userId, 'userCourseId' => $userCourseId, 'status' => 'done');
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET itemlist/{userId}/{userCourseId}
   */ 
	protected function getItemList($userId, $userCourseId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT *
	  		FROM view_user_course_item AS VUCI
	  		WHERE VUCI.userId = :userId
	  		  AND VUCI.userCourseId = :userCourseId
	  		ORDER BY VUCI.userCourseItemId DESC
	  	';
	  	$bind = array('userId' => $userId, 'userCourseId' => $userCourseId);
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET coursesection/{userId}/{courseId}
   */ 
	protected function getCourseSection($userId, $courseId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT CS.*, C.name
	  		  FROM course_section AS CS
	  		 INNER JOIN course AS C
	  		    ON C.courseId = CS.courseId 
	  		 INNER JOIN user_course AS UC
	  		    ON UC.courseId = C.courseId
	  		   AND UC.userId = :userId
	  		 WHERE CS.courseId = :courseId
	  		 ORDER BY CS.seq
	  	';
	  	$bind = array('userId' => $userId, 'courseId' => $courseId);
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET coursesectionItem/{userId}/{courseSectionId}
   */ 
	protected function getCourseSectionItem($userId, $courseSectionId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT *
	  		FROM view_course_item AS CI
	  		LEFT OUTER JOIN user_course_item AS UCI
	  		ON CI.courseItemId = UCI.courseItemId
	  		AND UCI.userId = :userId
	  		WHERE CI.courseSectionId = :courseSectionId
	  		ORDER BY CI.courseItemId DESC
	  	';
	  	$bind = array('userId' => $userId, 'courseSectionId' => $courseSectionId);
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
	
  /**
   * @url GET allcourseitem/{userId}/{courseId}
   */ 
	protected function getAllCourseItem($userId, $courseId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT *
	  		FROM view_course_item AS CI
	  		LEFT OUTER JOIN user_course_item AS UCI
	  		ON CI.courseItemId = UCI.courseItemId
	  		AND UCI.userId = :userId
	  		WHERE CI.courseId = :courseId
	  		ORDER BY CI.courseItemId DESC
	  	';
	  	$bind = array('userId' => $userId, 'courseId' => $courseId);
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url POST newitem
   */ 
	protected function postNewItem($userId, $userCourseId, $courseItemId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
			$statement = '
				INSERT INTO user_course_item (userId, userCourseId, courseItemId, actionCount, status, level, seq)
				SELECT :userId, :userCourseId, :courseItemId, actionCount, :status, level, seq
				  FROM view_course_item
				 WHERE courseItemId = :courseItemId
			';
			$bind = array(
				'userId'       => $userId, 
				'userCourseId' => $userCourseId,
				'courseItemId' => $courseItemId,
				'status'       => 'start'
			);

			\TTOMail::createAndSendAdmin('A user add new item', json_encode($bind));
			
			$itemCount = \Db::execute($statement, $bind);
			$userCourseItemId = \Db::getLastInsertId();
			
			$statement = '
				INSERT INTO user_course_item_detail (userCourseItemId, itemDetailId, status)
				SELECT :userCourseItemId, ID.itemDetailId, :status
				  FROM course_item AS CI
			 	 INNER JOIN item_detail AS ID
				    ON CI.itemId = ID.ItemId
				 WHERE courseItemId = :courseItemId
				   AND ID.isAction = 1
			';
			$bind = array(
				'userCourseItemId' => $userCourseItemId, 
				'courseItemId'     => $courseItemId,
				'status'           => 'start'
			);
			$itemDetailCount = \Db::execute($statement, $bind);

			$response = new \stdClass();
			$response->userCourseItemId = $userCourseItemId;
			$response->itemCount = $itemCount;
			$response->itemDetailCount = $itemDetailCount;
			return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET itemdetail/{userId}/{userCourseItemId}
   */ 
	protected function getItemDetail($userId, $userCourseItemId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
				SELECT UCI.*, CI.*, I.code, I.content, I.itemGroupId
				FROM user_course_item AS UCI
				INNER JOIN course_item AS CI
				   ON UCI.courseItemId = CI.courseItemId
				INNER JOIN item AS I
				   ON CI.itemId = I.itemId
				WHERE UCI.userCourseItemId = :userCourseItemId
	  	';
	  	$bind = array('userCourseItemId' => $userCourseItemId);
			$response = \Db::getRow($statement, $bind);
			$itemId = $response['itemId'];
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
   * @url GET courseitemdetail/{userId}/{courseItemId}
   */ 
	protected function getCourseItemDetail($userId, $courseItemId) 
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
   * @url POST newitemradio
   */ 
	protected function postNewItemRadio($userId, $userCourseItemId, $itemDetailId, $itemRadioId, $point, $actionCount) 
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
   * @url POST newitemselect
   */ 
	protected function postNewItemSelect($userId, $userCourseItemId, $itemDetailId, $point, $actionCount, array $allItemSelect) 
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
   * @url POST newiteminput
   */ 
	protected function postNewItemInput($userId, $userCourseItemId, $itemDetailId, $point, $actionCount, array $allItemInput) 
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

  /**
   * @url POST updateitemdone
   */ 
	protected function postUpdateItemDone($userId, $userCourseItemId) 
	{
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
			$statement = '
				UPDATE user_course_item
				   SET status = :status
				 WHERE userCourseItemId = :userCourseItemId
			';
			$bind = array(
				'userCourseItemId' => $userCourseItemId,
				'status'           => 'done'
			);
			\Db::execute($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url POST updateCourseItemLevel
   */ 
	protected function postUpdateCourseItemLevel(array $userCourseItemList) 
	{
  	if (\TTO::getRole() == 'admin') {
			foreach ($userCourseItemList as $userCourseItem) {
				$statement = '
		  		UPDATE user_course_item
		  		SET level = :level
		  		WHERE userCourseItemId = :userCourseItemId
				';
		  	$bind = array(
		  		'level'        => $courseItem['level'],
		  		'courseItemId' => $courseItem['courseItemId']
		  	);
				$row_update = \Db::execute($statement, $bind);
			}
	  	$response = new \stdClass();
			$response->update_status = 'done';
			return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}


}


