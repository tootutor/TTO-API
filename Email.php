<?php
class Email
{
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
	 * @url POST sendemailadmin/{userId}
	 */
	protected function postSendEmailAdmin($userId, $subject, $message)
  {
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
  		\TTOMail::createAndSendAdmin($subject, $message);
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

}
