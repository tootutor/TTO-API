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

  /**
   * @url GET user/{userId}
   */ 
  protected function getUserCourseList($userId, $categoryId) 
  {
    if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
      $statement = '
        SELECT C.*, UC.coin, UC.point
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
  
  
}

