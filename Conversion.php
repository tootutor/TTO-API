<?php
use Luracast\Restler\RestException;

class Conversion
{
	/**
   * @smart-auto-routing false
	 */

  /**
   * @url GET /
   */ 
	protected function getConvertItem() 
	{
  	if (\TTO::getRole() == 'admin') {
      $response = new \stdClass();
      
      $statement = 'SELECT itemId, code, content, itemTypeId FROM item';
      //$bind = array('itemTypeId' => $itemTypeId);
      $allItem = \Db::getResult($statement);

      foreach ($allItem as &$item) {
        $newItem = new \stdClass();
        
        switch ($item['itemTypeId']) {
          case 1:
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
            break;
          case 3:
            $newItem->question = $item['content'];
            $statement = 'SELECT content, isAnswer, point FROM item_select WHERE itemId = :itemId';
            $bind = array('itemId' => $item['itemId']);
            $newItem->allSelect = \Db::getResult($statement, $bind);
            
            foreach ($newItem->allSelect as &$select) {
              if ($select['isAnswer']) {
                $select['isAnswer'] = true;
              } else {
                $select['isAnswer'] = false;
              }
            }
            break;
          case 4:
            $newItem->question = $item['content'];
            $statement = 'SELECT question, answer, answerType, point FROM item_input WHERE itemId = :itemId';
            $bind = array('itemId' => $item['itemId']);
            $newItem->allInput = \Db::getResult($statement, $bind);
            break;
          default:
            break;
        }
        if ($item['itemTypeId'] == 1 || $item['itemTypeId'] == 3 || $item['itemTypeId'] == 4) {
          $content2 = json_encode($newItem, JSON_UNESCAPED_UNICODE);
        } else {
          $content2 = $item['content'];
        }
        $statement = '
          UPDATE item
          SET content2 = :content2
          WHERE itemId = :itemId
        ';
        $bind = array('itemId' => $item['itemId'], 'content2' => $content2);
        \Db::execute($statement, $bind);
        
        $item['content2'] = $content2;
      }
      return $allItem;
  	} else {
  		throw new RestException(401, 'No Authorize or Invalid request !!!');
  	}
	}
	
}