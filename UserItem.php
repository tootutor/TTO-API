<?php
use Luracast\Restler\RestException;

class UserItem
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET by-course/{userId}/{courseId}
   */ 
	protected function getByCourse($userId, $courseId) 
	{
      	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
    	  	$statement = '
    	  		SELECT *
    	  		FROM view_course_item AS CI
    	  		LEFT OUTER JOIN user_course_item AS UCI
    	  		ON CI.courseItemId = UCI.courseItemId
    	  		AND UCI.userId = :userId
    	  		WHERE CI.courseId = :courseId
    	  		ORDER BY CI.courseItemId
    	  	';
    	  	$bind = array('userId' => $userId, 'courseId' => $userCourseId);
    			return \Db::getResult($statement, $bind);
      	} else {
      		throw new RestException(401, 'No Authorize or Invalid request !!!');
      	}
	}

  /**
   * @url GET by-course-section/{userId}/{courseSectionId}
   */ 
	protected function getByCourseSection($userId, $courseSectionId) 
	{
      	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
    	  	$statement = '
    	  		SELECT *
    	  		FROM view_course_item AS CI
    	  		LEFT OUTER JOIN user_course_item AS UCI
    	  		ON CI.courseItemId = UCI.courseItemId
    	  		AND UCI.userId = :userId
    	  		WHERE CI.courseSectionId = :courseSectionId
    	  		ORDER BY CI.courseItemId
    	  	';
    	  	$bind = array('userId' => $userId, 'courseSectionId' => $courseSectionId);
    			return \Db::getResult($statement, $bind);
      	} else {
      		throw new RestException(401, 'No Authorize or Invalid request !!!');
      	}
	}

  /**
   * @url POST add-user-item
   */ 
	protected function postAddUserItem($userId, $userCourseId, $courseItemId) 
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
   * @url POST update-item-done
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

}


