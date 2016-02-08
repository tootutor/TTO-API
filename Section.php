<?php
use Luracast\Restler\RestException;

class Section
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET
   */ 
	protected function getAllSection($courseId) 
	{
  	$statement = 'SELECT * FROM section WHERE courseId = :courseId';
  	$bind = array('courseId' => $courseId);
		return \Db::getResult($statement, $bind);
	}

  /**
   * @url GET user/{userId}
   */ 
	protected function getAllUserSection($userId, $courseId) 
	{
  	$statement = '
      SELECT * 
        FROM section AS S
       WHERE S.courseId = :courseId
    ';
  	$bind = array('courseId' => $courseId);
		return \Db::getResult($statement, $bind);
	}

  /**
   * @url PUT /{sectionId}
   * @url PUT /{sectionId}/user/{userId}
   */ 
  protected function updateCourse($sectionId, $name, $description, $seq, $userId = null) 
  {
    if (\TTO::getRole() == 'admin') {
      $statement = '
        UPDATE section
        SET 
          name        = :name,
          description = :description,
          seq         = :seq
        WHERE
          sectionId   = :sectionId
      ';
      $bind = array(
        'sectionId'   => $sectionId,
        'name'        => $name,
        'description' => $description,
        'seq'         => $seq
      );
      $row_update = \Db::execute($statement, $bind);
      return;
    } else {
      throw new RestException(401, 'No Authorize or Invalid request !!!');
    }
  }

  /**
   * @url POST
   * @url POST /user/{userId}
   */ 
  protected function addSection($courseId, $name, $description, $seq, $userId = null) 
  {
    if (\TTO::getRole() == 'admin') {
      $statement = '
        INSERT INTO section (courseId, name, description, seq)
        VALUES (:courseId, :name, :description, :seq)
      ';
      $bind = array(
        'courseId'    => $courseId,
        'name'        => $name,
        'description' => $description,
        'seq'         => $seq
      );
      \Db::execute($statement, $bind);
      $sectionId = \Db::getLastInsertId();

      $statement = 'SELECT * FROM section WHERE sectionId = :sectionId';
      $bind = array('sectionId' => $sectionId);
      return \Db::getRow($statement, $bind);
      
    } else {
      throw new RestException(401, 'No Authorize or Invalid request !!!');
    }
  }
  
}

