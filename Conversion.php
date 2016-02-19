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

      foreach ($allItem as &$item) {
        $newItem = new \stdClass();
        $newItem->question = $item['content'];
        $statement = 'SELECT content, isAnswer, point FROM item_radio WHERE itemId = :itemId';
        $bind = array('itemId' => $item['itemId']);
        $newItem->allRadio = \Db::getResult($statement, $bind);
        
        foreach ($newItem->allRadio as &$radio) {
          if ($radio['isAnswer']) {
            $radio['isAnswer'] = true;
          } else {
            $radio['isAnswer'] = false;
          }
        }
        
        $content2 = json_encode($newItem, JSON_UNESCAPED_UNICODE);
        
        $statement = '
          UPDATE item
          SET content2 = :content2
          WHERE itemId = :itemId
        ';
        $bind = array('itemId' => $item['itemId'], 'content2' => $content2);
        \Db::execute($statement, $bind);
        
        $item['content2'] = $content2;
      }
      //$statement = 'SELECT itemId, code, content2 FROM item WHERE itemTypeId = :itemTypeId';
      //$bind = array('itemTypeId' => $itemTypeId);
			//return \Db::getResult($statement, $bind);
      return $allItem;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
	
}