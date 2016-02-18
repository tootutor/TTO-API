<?php
use Luracast\Restler\RestException;

class Conversion
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET /{itemTypeId}
   */ 
	protected function getConvertItem($itemTypeId) 
	{
  	if (\TTO::getRole() == 'admin') {
      $response = new \stdClass();
      
      $statement = 'SELECT itemId, code, content FROM item WHERE itemTypeId = :itemTypeId';
      $bind = array('itemTypeId' => $itemTypeId);
      $allItem = \Db::getResult($statement, $bind);

      foreach ($allItem as $item) {
        $newItem = new \stdClass();
        $newItem->question = $item['content'];
        $statement = 'SELECT content, isAnswer, point FROM item_radio WHERE itemId = :itemId';
        $bind = array('itemId' => $item['itemId']);
        $newItem->allRadio = \Db::getResult($statement, $bind);
        $content2 = json_encode($newItem);
        
        $statement = '
          UPDATE item
          SET content2 = :content2
          WHERE itemId = :itemId
        ';
        $bind = array('itemId' => $item['itemId'], 'content2' => $content2);
        \Db::execute($statement, $bind);
      }
			return;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
	
}