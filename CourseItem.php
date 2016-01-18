<?php
use Luracast\Restler\RestException;

class CourseItem
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET coursesection/{courseId}
   */ 
	protected function getCourseSection($courseId) 
	{
  	if (\TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT CS.*
	  		  FROM course_section AS CS
	  		 INNER JOIN course AS C
	  		    ON C.courseId = CS.courseId 
	  		 WHERE CS.courseId = :courseId
	  		 ORDER BY CS.seq
	  	';
	  	$bind = array('courseId' => $courseId);
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET coursesectionItem/{courseId}/{courseSectionId}
   */ 
	protected function getCourseSectionItem($courseId, $courseSectionId) 
	{
  	if (\TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT *
	  		FROM view_course_item AS CI
	  		WHERE CI.courseId = :courseId
	  		AND CI.courseSectionId = :courseSectionId
	  		ORDER BY CI.courseItemId DESC
	  	';
	  	$bind = array('courseId' => $courseId, 'courseSectionId' => $courseSectionId);
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
	
  /**
   * @url POST newcourseitem
   */ 
	protected function postNewCourseItem($courseId, array $itemList) 
	{
  	if (\TTO::getRole() == 'admin') {
			foreach ($itemList as $item) {
				$statement = '
		  		INSERT INTO course_item (courseId, itemId)
		  		VALUES (:courseId, :itemId)
				';
		  	$bind = array(
		  		'courseId' => $courseId,
		  		'itemId'   => $item['itemId']
		  	);
				$row_insert = \Db::execute($statement, $bind);
				$itemDetailId = \Db::getLastInsertId();
			}
	  	$response = new \stdClass();
			$response->insert_status = 'done';
			return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}

  /**
   * @url GET allcourseitem/{courseId}
   */ 
	protected function getAllCourseItem($courseId) 
	{
  	$statement = '
  		SELECT * 
  		FROM view_course_item AS VCI
  		WHERE VCI.courseId = :courseId 
  	';
  	$bind = array('courseId' => $courseId);
		return \Db::getResult($statement, $bind);
	}

  /**
   * @url POST updateCourseItemLevel
   */ 
	protected function postUpdateCourseItemLevel(array $courseItemList) 
	{
	  	if (\TTO::getRole() == 'admin') {
				foreach ($courseItemList as $courseItem) {
					$statement = '
			  		UPDATE course_item
			  		SET level = :level
			  		WHERE courseItemId = :courseItemId
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

