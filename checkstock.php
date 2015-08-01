<?php
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

ini_set("display_errors", 1);
set_time_limit(0);
umask(0);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

class Checkstatus
{
	public function setProdcollection()	{
		$this->collectionConfigurable =  Mage::getResourceModel('catalog/product_collection')->addAttributeToFilter('type_id', array('eq' => 'configurable'));
	}	
	public function setBackInStock()
	{
	    $collection = Mage::getResourceModel('cataloginventory/stock_item_collection');
	    $outQty = Mage::getStoreConfig('cataloginventory/item/options_min_qty');
	    $collection->addFieldToFilter('qty', array('gt' => $outQty));
	    $collection->addFieldToFilter('is_in_stock', 0);

	    foreach($collection as $item) {
	        $item->setData('is_in_stock', 1);
	    }
	    $collection->save();
	}
	public function setBackOutStock()
	{
	    $collection = Mage::getResourceModel('cataloginventory/stock_item_collection');
	    $outQty = Mage::getStoreConfig('cataloginventory/item/options_min_qty');
	    $collection->addFieldToFilter('qty', array('gt' => $outQty));
	    $collection->addFieldToFilter('is_in_stock', 0);

	    foreach($collection as $item) {
	        $item->setData('is_in_stock', 1);
	    }
	    $collection->save();
	}
}

?>