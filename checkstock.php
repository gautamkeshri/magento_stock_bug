<?php
class Checkstatus
{
	private $log_msg;
	
	public function setBackOutStock()
	{
		//$outQty = Mage::getStoreConfig('cataloginventory/item/options_min_qty');	
	    $collection = Mage::getResourceModel('cataloginventory/stock_item_collection');
	    $collection->addFieldToFilter('qty', array('lteq' => 4));
	    $collection->addFieldToFilter('is_in_stock', 1);

	    foreach($collection as $item) {
	    	$this->log_msg .= "Make Out:".$item->getProductId()." - Qty: ".$item->getQty()."\r\n";
	        $item->setData('is_in_stock', 0);
	    }
	    $collection->save();
	}
	public function setBackInStock()
	{
		//$outQty = Mage::getStoreConfig('cataloginventory/item/options_min_qty');
	    $collection = Mage::getResourceModel('cataloginventory/stock_item_collection');
	    $collection->addFieldToFilter('qty', array('gt' => 4));
	    $collection->addFieldToFilter('is_in_stock', 0);

		foreach($collection as $item) {
	    	$this->log_msg .= "Make In:".$item->getProductId()." - Qty: ".$item->getQty()."\r\n";
	        $item->setData('is_in_stock', 1);
	    }
	    $collection->save();
	
	}
	public function main()
	{
		$this->log_msg="";
		$this->setBackOutStock();
		$this->setBackInStock();
		return $this->log_msg;
	}
}
?>
