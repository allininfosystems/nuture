<?php

namespace Cminds\StockNotification\Controller\Custom;

use Cminds\StockNotification\Model\ResourceModel\StockNotification\CollectionFactory
    as StockNotificationCollectionFactory;
use Cminds\StockNotification\Model\StockNotificationFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Validator\EmailAddress;

class Index extends Action
{
    public function execute()
    {
		$pid=$_REQUEST['pid'];
		$email=$_REQUEST['email'];
		$cid=$_REQUEST['cid'];
		
		if ($this->checkNotification($pid,$email,$cid) === false) {
            $this->saveNotification($pid,$email,$cid);
			echo '<p style="color:green;">We will send an email to '.$email.' when the product will back in stock!</p>';
        } else {
            echo '<p style="color:red;">Your e-mail '.$email.' is already on the list!</p>';
        }
    }
	public function checkNotification($pid,$email,$cid){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$sql = "SELECT * from `cminds_stocknotification_request` where product_id='".$pid."' and email='".$email."' and customer_id='".$cid."' ";
		$result = $connection->fetchAll($sql);
		$res=false;
		if(count($result)>0){
			$res=true;
		}
        return $res;
    }
	public function saveNotification($pid,$email,$cid){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$tableName='cminds_stocknotification_request';
		$sql = "INSERT INTO " . $tableName . " SET product_id = ".$pid.", email = '".$email."',customer_id = '".$cid."',notified = 0 ";
        $connection->query($sql);
    }
}
