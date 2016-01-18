<?php
use Luracast\Restler\RestException;

class App
{
  /**
   * @smart-auto-routing false
   */ 

	/**
	 * @url GET appinfo
	 */
  protected function getAppInfo()
  {
  	$response = new \stdClass();
  	$response->minVersion = 'V201512232000';
  	$response->maxVersion = 'V201512232000';
  	return $response;
  }
  
}

