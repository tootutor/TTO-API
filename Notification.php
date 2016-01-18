<?php
use Luracast\Restler\RestException;

class Notification
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET allnotification/{userId}
   */ 
	protected function getAllNotification($userId) 
	{
  	$statement = '
  		SELECT N.*, U.nickname, FU.nickname AS fromNickname
  		FROM notification AS N
  		INNER JOIN user AS U
  		   ON U.userId = N.userId
      INNER JOIN user AS FU
         ON FU.userId = N.fromUserId
  		WHERE N.userId = :userId
  		ORDER BY N.timestamp DESC
  	';
  	$bind = array('userId' => $userId);
		return \Db::getResult($statement, $bind);
	}

  /**
   * @url POST newnotification
   */ 
	protected function postNewNotification($userId, $routeUrl, $fromUserId, $content) 
	{
		$statement =
		'SELECT 1 FROM notification
			 WHERE userId     = :userId
			   AND routeUrl   = :routeUrl
			   AND fromUserId = :fromUserId
		LIMIT 1
		';
		$bind = array(
			'userId'     => $userId, 
			'routeUrl'   => $routeUrl,
			'fromUserId' => $fromUserId
		);
		$exist = \Db::getValue($statement, $bind);
		
		if ($exist) {
			$statement = '
				UPDATE notification
				   SET content = :content
			   WHERE userId     = :userId
			     AND routeUrl   = :routeUrl
			     AND fromUserId = :fromUserId
			';
			$bind = array(
				'userId'     => $userId, 
				'routeUrl'   => $routeUrl,
				'fromUserId' => $fromUserId,
				'content'    => $content
			);
			\Db::execute($statement, $bind);
		} else {
			$statement = '
				INSERT INTO notification (userId, routeUrl, fromUserId, content)
				VALUES (:userId, :routeUrl, :fromUserId, :content)
			';
			$bind = array(
				'userId'     => $userId, 
				'routeUrl'   => $routeUrl,
				'fromUserId' => $fromUserId, 
				'content'    => $content
			);
			\Db::execute($statement, $bind);
			$statement = '
				UPDATE user SET notificationCount = notificationCount + 1
				WHERE userId = :userId
			';
			$bind = array('userId' => $userId);
			\Db::execute($statement, $bind);
		}
	}

  /**
   * @url POST removenotification
   */ 
	protected function postRemoveNotification($userId, $routeUrl, $fromUserId) 
	{
		$statement = '
			DELETE FROM notification
			 WHERE userId     = :userId
			   AND routeUrl   = :routeUrl
			   AND fromUserId = :fromUserId
		';
		$bind = array(
			'userId'     => $userId, 
			'routeUrl'   => $routeUrl,
			'fromUserId' => $fromUserId
		);
		\Db::execute($statement, $bind);

		$statement = '
			UPDATE user SET notificationCount = notificationCount - 1
			WHERE userId = :userId
		';
		$bind = array('userId' => $userId);
		\Db::execute($statement, $bind);
	}
	
}