<?php
use Luracast\Restler\RestException;

class UserSection
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET item/{courseSectionId}
   */ 
	protected function getItem($courseSectionId) 
	{
      	if (\TTO::getRole() == 'admin') {
    	  	$statement = '
    	  		SELECT *
    	  		FROM view_course_item AS CI
    	  		WHERE CI.courseSectionId = :courseSectionId
    	  		ORDER BY CI.courseItemId DESC
    	  	';
    	  	$bind = array('courseSectionId' => $courseSectionId);
    			return \Db::getResult($statement, $bind);
      	} else {
      		throw new RestException(401, 'No Authorize or Invalid request !!!');
      	}
	}
}

