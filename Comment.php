<?php
use Luracast\Restler\RestException;

class Comment
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET allcomment/{userCourseItemId}
   */ 
	protected function getAllComment($userCourseItemId) 
	{
  	$statement = '
  		SELECT C.*, U.nickname, U.avatarId
  		FROM comment AS C
  		INNER JOIN user AS U
  		   ON U.userId = C.userId
  		WHERE userCourseItemId = :userCourseItemId
  		ORDER BY C.timestamp
  	';
  	$bind = array('userCourseItemId' => $userCourseItemId);
		return \Db::getResult($statement, $bind);
	}

  /**
   * @url POST newcomment
   */ 
	protected function postNewComment($userCourseItemId, $userId, $message) 
	{
		$statement = '
			INSERT INTO comment (userCourseItemId, userId, message)
			VALUES (:userCourseItemId, :userId, :message)
		';
		$bind = array(
			'userCourseItemId' => $userCourseItemId, 
			'userId' => $userId, 
			'message' => $message
		);
		
		\TTOMail::createAndSendAdmin('A user comment on an item', json_encode($bind));
		
		\Db::execute($statement, $bind);
	}

  /**
   * @url GET allcommentdetail/{commenHeaderId}
   */ 
	protected function getAllCommentDetail($commenHeaderId) 
	{
  	$statement = '
  		SELECT CD.*, U.nickname, U.avatarId
  		FROM comment_detail AS CD
  		INNER JOIN user AS U
  		   ON U.userId = C.userId
  		WHERE commentHeaderId = :commentHeaderId
  		ORDER BY CD.timestamp
  	';
  	$bind = array('commentHeaderId' => $commentHeaderId);
		return \Db::getResult($statement, $bind);
	}

  /**
   * @url POST newcommentdetail
   */ 
	protected function postNewCommentDetail($commentHeaderId, $userId, $message) 
	{
		if ($commentHeaderId <= 0) {
			$statement = '
				INSERT INTO comment_header () VALUES ()
			';
			\Db::execute($statement);
			$commentHeaderId = \Db::getLastInsertId();
		}
		
		$statement = '
			INSERT INTO comment_detail (comment_header_id, userId, message)
			VALUES (:commentHeaderId, :userId, :message)
		';
		$bind = array(
			'commentHeaderId' => $commentHeaderId, 
			'userId' => $userId, 
			'message' => $message
		);
		
		\TTOMail::createAndSendAdmin('A user comment on an item', json_encode($bind));
		
		\Db::execute($statement, $bind);

  	$response = new \stdClass();
		$response->commentHeaderId = $commentHeaderId;
		return $response;
	}
	
}