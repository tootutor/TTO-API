<?php
use Luracast\Restler\RestException;

class Coin
{
  /**
   * @smart-auto-routing false
   */ 

	/**
	 * @url POST neworder
	 */
	protected function postNewOrder($userId, $coin, $bonus, $amount)
  {
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
			$statement = "
	  		INSERT INTO coin_order (userId, coin, bonus, amount, status, bankId)
	  		VALUE (:userId, :coin, :bonus, :amount, :status, :bankId)
	  	";
			$bind = array (
	  		'userId' => $userId,
	  		'coin'   => $coin,
	  		'bonus'  => $bonus,
	  		'amount' => $amount,
	  		'status' => 'order',
	  		'bankId' => 1
	  	);
	    $row_insert = \Db::execute($statement, $bind);

			\TTOMail::createAndSendAdmin('A user ordered coin', json_encode($bind));

			if ($row_insert > 0) { 
		    $last_insert_id = \Db::getLastInsertId();
		  	$statement = 'SELECT * FROM coin_order WHERE coinOrderId = :coinOrderId';
		  	$bind = array('coinOrderId' => $last_insert_id);
				return \Db::getResult($statement, $bind);
			} else {
	  		throw new RestException(500, 'New Order Error !!!');
			}
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }

	/**
	 * @url GET allorder
	 */
  protected function getAllOrder()
  {
  	if (\TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT UC.*, U.nickname 
	  		  FROM coin_order AS UC
	  		 INNER JOIN user AS U
	  		    ON UC.userId = U.userId
	  		 ORDER BY status DESC
	  	';
			return \Db::getResult($statement);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }

	/**
	 * @url GET orderlist/{userId}
	 */
  protected function getOrderList($userId)
  {
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = 'SELECT * FROM coin_order WHERE userId = :userId ORDER BY status DESC';
	  	$bind = array('userId' => $userId);
			return \Db::getResult($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }

	/**
	 * @url GET mycoin/{userId}
	 * @url GET user/{userId}
	 */
  protected function getUserCoin($userId)
  {
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = 'SELECT coin FROM user WHERE userId = :userId';
	  	$bind = array('userId' => $userId);
			return \Db::getRow($statement, $bind);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }

	/**
   * @url POST confirmorder/{coinOrderId}
   */ 
  protected function postConfirmOrder($coinOrderId, $userId, $bankId, $transferAmount, $transferDate)
  {
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
	  		UPDATE coin_order SET 
	  			status         = :status, 
	  			bankId         = :bankId, 
	  			transferAmount = :transferAmount,
	  			transferDate   = :transferDate
	  		WHERE coinOrderId = :coinOrderId
	  	';
	  	$bind = array(
	  		'coinOrderId'    => $coinOrderId, 
	  		'bankId'         => $bankId, 
	  		'transferAmount' => $transferAmount,
	  		'transferDate'   => $transferDate,
	  		'status'         => 'confirm'
	  	);
			$count = \Db::execute($statement, $bind);

			\TTOMail::createAndSendAdmin('A user confirmed order', json_encode($bind));

			if ($count > 0) {
				$statement = 'SELECT * FROM coin_order WHERE coinOrderId = :coinOrderId';
				$bind = array('coinOrderId' => $coinOrderId);
				return \Db::getRow($statement, $bind);
			} else {
	  		throw new RestException(500, 'Confirm error!!!');
			}			
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }

	/**
   * @url POST cancelorder/{coinOrderId}
   */ 
  protected function postCancelOrder($coinOrderId, $userId)
  {
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = 'DELETE coin_order WHERE coinOrderId = :coinOrderId';
	  	$bind = array('coinOrderId' => $coinOrderId);
			$count = \Db::execute($statement, $bind);

			\TTOMail::createAndSendAdmin('A user cancelled order', json_encode($bind));
			
			if ($count > 0) {
		  	$response = new \stdClass();
		  	$response->cancel = $count;
		  	return $response;
			} else {
	  		throw new RestException(500, 'Cancel Error !!!');
			}
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }
  
  /**
   * @url POST approveorder/{coinOrderId}
   */ 
  protected function postApproveOrder($coinOrderId, $userId)
  {
  	if (\TTO::getRole() == 'admin') {
	  	$statement = 'UPDATE coin_order SET status = :status WHERE coinOrderId = :coinOrderId';
	  	$bind = array('coinOrderId' => $coinOrderId, 'status' => 'approve');
			$count = \Db::execute($statement, $bind);

			\TTOMail::createAndSendAdmin('Admin approved an order', json_encode($bind));
			\TTOMail::createAndSend(ADMINEMAIL, \TTO::getUserEmail($userId), 'Admin have approved your order', 'Please check on the system');

			if ($count > 0) {
				$statement = 'SELECT coin + bonus FROM coin_order WHERE coinOrderId = :coinOrderId';
				$bind = array('coinOrderId' => $coinOrderId);
				$coin = \Db::getValue($statement, $bind);

		  	$statement = 'UPDATE user SET coin = coin + :coin WHERE userId = :userId';
		  	$bind = array('userId' => $userId, 'coin' => $coin);
				$count = \Db::execute($statement, $bind);
			} else {
	  		throw new RestException(500, 'Approve Error !!!');
			}
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }
	
  /**
   * @url GET
   * @url GET allpackage
   */ 
	protected function getAllPackage() 
	{
  	$statement = 'SELECT * FROM coin WHERE status = :status ORDER BY amount';
  	$bind = array('status' => 'active');
		return \Db::getResult($statement, $bind);
	}
}

