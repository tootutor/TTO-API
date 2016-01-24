<?php
use Luracast\Restler\RestException;

class User 
{
	/**
	 * @url POST register
	 * @url POST
	 */ 
	function postRegister(
		$email, $password, $firstname, $lastname, $nickname, $phone, $birthdate, $school, $province, $level, $purpose, $avatarId)
  {
  	//Hash password
  	$hash   = password_hash($password, PASSWORD_DEFAULT);
  	$serial = sha1(uniqid());
  	
		$statement = "
  		INSERT INTO user 
  			(email, role, hash, serial, firstname, lastname, nickname, phone, birthdate, school, province, level, purpose, avatarId)
  		VALUE 
  			(:email, :role, :hash, :serial, :firstname, :lastname, :nickname, :phone, :birthdate, :school, :province, :level, :purpose, :avatarId)
  	";
	
		$bind = array (
  		'email'     => $email,
  		'role'      => 'student',
  		'hash'      => $hash,
  		'serial'    => $serial,
  		'firstname' => $firstname,
  		'lastname'  => $lastname,
  		'nickname'  => $nickname,
  		'phone'     => $phone,
  		'birthdate' => $birthdate,
  		'school'    => $school,
  		'province'  => $province,
  		'level'     => $level,
  		'purpose'   => $purpose,
  		'avatarId'  => $avatarId
  	);

    $count = \Db::execute($statement, $bind);
    $userId = \Db::getLastInsertId();

		\TTOMail::createAndSendAdmin('A new user registered', json_encode($bind));
		\TTOMail::createAndSend(ADMINEMAIL, \TTO::getEmail(), 'You have registered to Too Tutor Online', 'Your serial number : ' + $serial);

  	$response = new \stdClass();
  	$response->count = $count;
  	$response->userId = $last_insert_id;
  	return $response;
  }

	/**  
   * @url GET profile/{userId}
   * @url GET {userId}
   */ 
  protected function getProfile($userId)
  {
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
        SELECT firstname, lastname, nickname, phone, birthdate, school, province, level, purpose, avatarId, coin, point, role, status
        FROM user WHERE userId = :userId
      ';
	  	$bind = array('userId' => $userId);
			return \Db::getRow($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }

	/**
	 * @url POST profile/{userId}
	 * @url PUT {userId}
	 */ 
  protected function postProfile(
  			$userId, $firstname, $lastname, $nickname, $phone, $birthdate, $school, $province, $level, $purpose, $avatarId)
  {
  	if ($userId == \TTO::getUserId()) {
	  	$statement = '
		  	UPDATE user SET
			  	firstname = :firstname,
			  	lastname  = :lastname, 
			  	nickname  = :nickname,
			  	phone     = :phone,
			  	birthdate = :birthdate,
			  	school    = :school,
			  	province  = :province,
			  	level     = :level,
			  	purpose   = :purpose,
			  	avatarId  = :avatarId
		  	WHERE userId = :userId
	  	';
	  	$bind = array(
		  	'firstname' => $firstname,
		  	'lastname'  => $lastname, 
		  	'nickname'  => $nickname,
		  	'phone'     => $phone,
		  	'birthdate' => $birthdate,
		  	'school'    => $school,
		  	'province'  => $province,
		  	'level'     => $level,
		  	'purpose'   => $purpose,
		  	'avatarId'  => $avatarId,
	  		'userId'    => $userId
	  	);
			$row_update = \Db::execute($statement, $bind);
			\TTOMail::createAndSendAdmin('A user updated profile', json_encode($bind));
			
	  	$response = new \stdClass();
	  	$response->row_update = $row_update;
	  	return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }

	/**  
   * @url GET alluser
   * @url GET
   */ 
  protected function getAllUser()
  {
  	if (\TTO::getRole() == 'admin') {
	  	$statement = 'SELECT * FROM user';
			return \Db::getResult($statement);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }
	
	/**  
   * @url GET allAvatar
   */ 
  function getAllAvatar()
  {
  	$statement = 'SELECT * FROM avatar';
		return \Db::getResult($statement);
  }
}

