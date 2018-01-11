<?php

namespace Nuluv\Pincode\Controller\Index;

class Index extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
        $zip=$this->getRequest()->getParam('postcode');
	    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$sql = "SELECT * from `pincodelist` where pincode='".$zip."' ";
		$html='';
		$result = $connection->fetchAll($sql);
		if(count($result)>0){
			$message=$result[0]['message'];
			$html='<p>Estimated delivery time is '.$message.'</p>';
		}
		echo $html;
    }
}