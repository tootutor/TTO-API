<?php
use Luracast\Restler\iAuthenticate;
use Luracast\Restler\RestException;

class Auth implements iAuthenticate
{
  function __isAllowed()
  {
  	//Get Token from header and check against server side
  	$header = getallheaders();
  	if (isset($header['Token']) || isset($header['token'])) {
	  	$token = $header['Token'] ? $header['Token'] : $header['token'];
			//Check token against db then hold the token in static class for referncing later	  	
	  	$statement = 'SELECT userId, token, role, email FROM user WHERE token = :token';
	  	$bind = array('token' => $token);
			$row = Db::getRow($statement, $bind);
	  	if ($row['userId'] > 0) {
	  		\TTO::setUserId($row['userId']);
	  		\TTO::setToken($row['token']);
	  		\TTO::setRole($row['role']);
	  		\TTO::setEmail($row['email']);
 				return true;
	  	} else {
	  		return false;
	  	}
  	} else {
	 		return false;
  	}
  }
  
  public function __getWWWAuthenticateString()
  {
    return 'Query name="token"';
  }

	/**  
   * @url POST login
   */ 
  function postLogin($email, $password)
  {
  	//validate email and password
  	$statement = 'SELECT userId, hash, role, nickname, notificationCount, avatarId FROM user where email = :email';
  	$bind = array ('email' => $email);
  	$user = \Db::getRow($statement, $bind);

		\TTOMail::createAndSendAdmin('A user is logging in', json_encode($bind));

		if (password_verify($password, $user['hash'])) {
	  	//generate token
	  	$token = md5(uniqid(mt_rand(), true));
			//update token to db
			$statement = 'UPDATE user SET token = :token WHERE userId = :userId';
			$bind = array ('token' => $token, 'userId' => $user['userId']);
	    \Db::execute($statement, $bind);
	  	//then return token
	  	$response = new \stdClass();
	  	$response->userId = $user['userId'];
	  	$response->token  = $token;
	  	$response->role   = $user['role'];
	  	$response->nickname = $user['nickname'];
	  	$response->notificationCount = $user['notificationCount'];
	  	$response->avatarId = $user['avatarId'];
	  	return $response;
		} else {
			throw new RestException(401, 'Invalid email or password !!!');
		}
  }

	/**  
   * @url GET current/{userId}
   */ 
  protected function getCurrent($userId) 
  {
  	if ($userId == \TTO::getUserId()) {
	  	$response = new \stdClass();
	  	$response->userId = \TTO::getUserId();
			$response->token  = \TTO::getToken();
			return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }

	/**
	 * @url POST logout
	 */ 
  protected function postLogout($userId)
  {
  	if ($userId == \TTO::getUserId()) {
			//update token to db
			$statement = 'UPDATE user SET token = :token WHERE userId = :userId';
			$bind = array ('token' => '', 'userId' => $userId);
	    $count = \Db::execute($statement, $bind);
	  	//then return token
	  	$response = new \stdClass();
	  	$response->count = $count;
	  	return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }
}
