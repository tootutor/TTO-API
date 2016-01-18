<?php
class Test
{
  protected function postPasswordHash($password) {
  	$response = new \stdClass();
  	$response->pwdhash = password_hash($password, PASSWORD_DEFAULT);
  	
  	return $response;
  }
  
  protected function postValidatePassword($password, $hash) {
  	$response = new \stdClass();
		if (password_verify($password, $hash)) {
		    $response->check = 'Password is valid!';
		} else {
		    $response->check =  'Invalid password.';
		}

  	return $response;
  }
  
  protected function postMD5($password) {
  	$response = new \stdClass();
  	$response->pwdmd5 = md5($password);
  	
  	return $response;
  }
  
  protected function getFullPath() {
  	$response = new \stdClass();
  	$response->fullpath = dirname(__FILE__);

  	return $response;
  }

  protected function getAllRequest() {
  	$response = new \stdClass();
  	$response->request = $_REQUEST;
  	$response->get = $_GET;
  	$response->post = $_POST;
  	$response->header = getallheaders();
  	
  	return $response;
  }

  protected function getServer() {
  	$response = new \stdClass();
  	$response->server = $_SERVER;

  	return $response;
  }

  protected function postJsonArray($field, array $dataList) {
  	$response = new \stdClass();
  	$response->field = $field;
  	$response->array = $dataList;

  	return $response;
  }

  protected function postArray(array $dataList) {
  	$response = new \stdClass();

  	foreach ($dataList as $data) {
	  	$response->id          = $data['id'];
	  	$response->name        = $data['name'];
	  	$response->description = $data['description'];
	  	$response->coin        = $data['coin'];
	  	$response->status      = $data['status'];
	  	return $response;
  	}
  }

	/**
	 * @url POST sendemail
	 */
	protected function postSendEmail($from, $to, $subject, $message)
  {
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
  		\TTOMail::createAndSend($from, $to, $subject, $message);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }

	/**
	 * @url GET useremail/{userId}
	 */
	protected function getUserEmail($userId)
  {
  	if (\TTO::getRole() == 'admin') {
	  	$response = new \stdClass();
	  	return \TTO::getUserEmail($userId);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }

	/**
	 * @url GET something/{id}
	 */
	protected function getSomething($id)
  {
  	$response = new \stdClass();
  	$response->action = 'GET';
  	$response->id = $id;
  	return $response;
  }

	/**
	 * @url POST something
	 */
	protected function postSomething($data)
  {
  	$response = new \stdClass();
  	$response->action = 'POST';
  	$response->data = $data;
  	return $response;
  }

	/**
	 * @url PUT something/{id}
	 */
	protected function putSomething($id, $data)
  {
  	$response = new \stdClass();
  	$response->action = 'PUT';
  	$response->id = $id;
  	$response->data = $data;
  	return $response;
  }

	/**
	 * @url DELETE something/{id}
	 */
	protected function deleteSomething($id)
  {
  	$response = new \stdClass();
  	$response->action = 'DELETE';
  	$response->id = $id;
  	return $response;
  }

}
