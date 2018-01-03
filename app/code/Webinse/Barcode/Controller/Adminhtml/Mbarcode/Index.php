<?php
namespace Webinse\Barcode\Controller\Adminhtml\Mbarcode;

use Magento\Backend\App\Action;

class Index extends Action
{
    public function execute()
    {
		$barcode=$_REQUEST['barcode'];
		$sku=$_REQUEST['sku'];
		/*echo 'sku'.$sku;
		echo 'barcode'.$barcode;*/
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
		$connection = $resource->getConnection();
		$sql = "SELECT * from `webinse_barcode_generated_barcodes` where sku='".$sku."' and barcode='".$barcode."' ";
		$result = $connection->fetchAll($sql);
		$html='<p style="color:red;">Barcode is not match with product</p>';
		if(count($result)>0){
			$html='<p style="color:green;">Barcode is match with product</p>';
		}
		echo $html;
    }
}