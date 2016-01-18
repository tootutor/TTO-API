<?php
use Luracast\Restler\RestException;

class Course
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET
   * @url GET allcourse
   */ 
	protected function getAllCourse() 
	{
  	$statement = 'SELECT * FROM view_course_summary WHERE status = :status';
  	$bind = array('status' => 'active');
		return \Db::getResult($statement, $bind);
	}

  /**
   * @url GET {courseId}
   */ 
	protected function getCourse($courseId) 
	{
  	$statement = 'SELECT * FROM view_course_summary WHERE courseId = :courseId';
  	$bind = array('courseId' => $courseId);
		return \Db::getResult($statement, $bind);
	}

}

