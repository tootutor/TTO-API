<?php
use Luracast\Restler\RestException;

class Course
{
  /**
   * @smart-auto-routing false
   */

  /**
   * @url GET
   */ 
  protected function getCourseList($categoryId) 
  {
    $statement = 'SELECT * FROM course WHERE categoryId = :categoryId';
    $bind = array('categoryId' => $categoryId);
    return \Db::getResult($statement, $bind);
  }

  /**
   * @url GET user/{userId}
   */ 
  protected function getUserCourseList($userId, $categoryId) 
  {
    if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
      $statement = '
        SELECT C.*, UC.userId, UC.coin, UC.point
          FROM user_course AS UC
         INNER JOIN course AS C
            ON UC.courseId = C.courseId
         WHERE UC.userId = :userId 
           AND C.categoryId = :categoryId
      ';
      $bind = array('userId' => $userId, 'categoryId' => $categoryId);
      return \Db::getResult($statement, $bind);
    } else {
      throw new RestException(401, 'No Authorize or Invalid request !!!');
    }
  }
  
  /**
   * @url PUT /{courseId}
   * @url PUT /{courseId}/user/{userId}
   */ 
  protected function updateCourse($courseId, $code, $categoryId, $name, $description, $coin, $status, $userId = null) 
  {
    if (\TTO::getRole() == 'admin') {
      $statement = '
        UPDATE course
        SET 
          code        = :code,
          categoryId  = :categoryId,
          name        = :name,
          description = :description,
          coin        = :coin,
          status      = :status
        WHERE
          courseId    = :courseId
      ';
      $bind = array(
        'courseId'    => $courseId,
        'code'        => $code,
        'categoryId'  => $categoryId,
        'name'        => $name,
        'description' => $description,
        'coin'        => $coin,
        'status'      => $status
      );
      $row_update = \Db::execute($statement, $bind);
      return;
    } else {
      throw new RestException(401, 'No Authorize or Invalid request !!!');
    }
  }

}

