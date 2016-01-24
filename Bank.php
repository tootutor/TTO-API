<?php
use Luracast\Restler\RestException;

class Bank
{
  /**
   * @smart-auto-routing false
   * @url getallbank
   * @url GET
   */ 
	protected function getAllBank() 
	{
  	$statement = 'SELECT * FROM bank WHERE status = :status';
  	$bind = array('status' => 'active');
		return \Db::getResult($statement, $bind);
	}
}

