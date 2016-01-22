<?php
use Luracast\Restler\RestException;

class Section
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET course/{courseId}
   */ 
	protected function getAllSection($courseId) 
	{
  	$statement = 'SELECT * FROM section WHERE courseId = :courseId';
  	$bind = array('courseId' => $courseId);
		return \Db::getResult($statement, $bind);
	}

}

