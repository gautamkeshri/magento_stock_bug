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
	public function setBackInStockConfig()
	{
		$stockCollection = Mage::getModel('cataloginventory/stock_item')->getCollection()
                        //->addFieldToFilter('is_in_stock', Mage_CatalogInventory_Model_Stock_Status::STATUS_OUT_OF_STOCK)
                        ->addFieldToFilter('type_id', 'configurable')->load(true);
        foreach ($stockCollection as $stockObject) {
            $childStockQty = 0;
            $product = mage::getModel('catalog/product')->load($stockObject->getProductId());
            if ($product->getSku() != '' && $product->getTypeId() == 'configurable') {
                $children = $product->getTypeInstance()->getUsedProducts();
                if (count($children) > 0) {
                    foreach ($children as $child) {
                        $childStock = $child->getStockItem();
                        $childStockQty += $childStock->getQty();
                    }


                    if ($childStockQty > 0 && $product->getIsInStock() == Mage_CatalogInventory_Model_Stock_Status::STATUS_OUT_OF_STOCK) {
                        echo 'Found configurable that is out of stock, but has stock children : setting in stock. ' . $product->getSku() . "\n";
                        mage::log('Found configurable that is out of stock, but has stock children : setting in stock. ' . $product->getSku());
                        if ($this->getArg('dry-run') == false) {
                            $stockObject->setIsInStock(True);
                            $stockObject->save();
                            $this->_doReindexFlag = true;
                        }
                    } elseif ($childStockQty == 0 && $product->getIsInStock() == Mage_CatalogInventory_Model_Stock_Status::STATUS_IN_STOCK) {
                        echo 'Found configurable that is in stock , but has no stock children : setting out of stock. ' . $product->getSku() . "\n";
                        mage::log('Found configurable that is in stock , but has no stock children : setting out of stock. ' . $product->getSku());
                        if ($this->getArg('dry-run') == false) {
                            $stockObject->setIsInStock(False);
                            $stockObject->save();
                            $this->_doReindexFlag = true;
                        }
                    }
                } else {
                    if ($product->getSku() != '') {
                        echo 'Found configurable that has no children : setting out of stock. ' . $product->getSku() . "\n";
                        mage::log('Found configurable that has no children : setting out of stock. ' . $product->getSku());
                        if ($this->getArg('dry-run') == false) {
                            $stockObject->setIsInStock(False);
                            $stockObject->save();
                            $this->_doReindexFlag = true;
                        }
                    }
                }
            } else {
                //
            }
	}
	public function main()
	{
		$this->log_msg="";
		$this->setBackOutStock();
		$this->setBackInStock();
		$this->setBackInStockConfig();
		return $this->log_msg;
	}
}
?>
