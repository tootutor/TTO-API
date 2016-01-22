<?php
use Luracast\Restler\RestException;

class Order
{
  /**
   * @smart-auto-routing false
   */ 

	/**
	 * @url POST /order
	 */
	protected function postOrder($userId, $coin, $bonus, $amount)
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
	 * @url GET
	 */
  protected function getAllOrder()
  {
  	if (\TTO::getRole() == 'admin') {
	  	$statement = '
	  		SELECT O.*, U.nickname 
	  		  FROM order AS O
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
	 * @url GET user/{userId}
	 */
  protected function getAllUserOrder($userId)
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
   * @url PUT {orderId}
   * @url PUT {orderId}/user/{userId}
   */ 
  protected function putOrder($orderId, $userId, $bankId, $transferAmount, $transferDate)
  {
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
	  	$statement = '
	  		UPDATE order SET 
	  			status         = :status, 
	  			bankId         = :bankId, 
	  			transferAmount = :transferAmount,
	  			transferDate   = :transferDate
	  		WHERE coinOrderId = :coinOrderId
	  	';
	  	$bind = array(
	  		'orderId'    => $orderId, 
	  		'bankId'         => $bankId, 
	  		'transferAmount' => $transferAmount,
	  		'transferDate'   => $transferDate,
	  		'status'         => 'confirm'
	  	);
			$count = \Db::execute($statement, $bind);

			\TTOMail::createAndSendAdmin('Updated order', json_encode($bind));
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }

	/**
   * @url DELETE {orderId}
   */ 
  protected function deleteOrder($orderId)
  {
    $statement = 'DELETE order WHERE orderId = :orderId';
    $bind = array('orderId' => $orderId);
    $count = \Db::execute($statement, $bind);

    \TTOMail::createAndSendAdmin('A user cancelled order', json_encode($bind));
    
    if ($count > 0) {
      return;
    } else {
      throw new RestException(500, 'Cancel Error !!!');
    }
  }

	/**
   * @url DELETE {orderId}/user/{userId}
   */ 
  protected function deleteUserOrder($orderId, $userId)
  {
  	if ($userId == \TTO::getUserId() || \TTO::getRole() == 'admin') {
      $this->deleteOrder($orderId);
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
  }
  
  /**
   * @url POST /approveorder/{orderId}
   */ 
  protected function postApproveOrder($orderId, $userId)
  {
  	if (\TTO::getRole() == 'admin') {
	  	$statement = 'UPDATE order SET status = :status WHERE coinOrderId = :coinOrderId';
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
	
}