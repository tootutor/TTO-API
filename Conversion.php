<?php
use Luracast\Restler\RestException;

class Task
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET /item
   */ 
	protected function getConvertItem() 
	{
  	if (\TTO::getRole() == 'admin') {
      $statement = 'SELECT * FROM item WHERE itemTypeId = :itemTypeId';
      $bind = array('itemTypeId' => $itemTypeId);
      $allItem = \Db::getResult($statement, $bind);

      foreach ($allItem as $item) {
        if ($item['itemTypeId'] == 1) {
          $statement = 'SELECT * FROM item_radio WHERE itemId = :itemId';
          $bind = array('itemId' => $item['itemId']);
          $allItemRadio = \Db::getResult($statement, $bind);
          $item += array('allItemRadio' => $allItemRadio);
        }
      }
      
	  	$response = new \stdClass();
			$response->insert_status = 'done';
			return $response;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
	
}