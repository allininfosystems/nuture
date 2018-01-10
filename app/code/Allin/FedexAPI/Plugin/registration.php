<?php
namespace Allin\FedexAPI\Plugin;

class PluginBefore
{
	
    public function beforePushButtons(
        \Magento\Backend\Block\Widget\Button\Toolbar\Interceptor $subject,
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    ) {

$_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); //instance of\Magento\Framework\App\ObjectManager
$storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
$orderid = $_objectManager->get('\Magento\Framework\App\RequestInterface')->getParam('order_id'); 
$directoryList = $_objectManager->get('\Magento\Framework\App\Filesystem\DirectoryList')->getPath('media').'/fedexpdf/'.$orderid.'-shiplabel.pdf'; 
//echo $orderid.'**************';
$currentStore = $storeManager->getStore();
$mediaUrl = $currentStore->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
$location = $mediaUrl.'fedexpdf/'.$orderid.'-shiplabel.pdf';
        $this->_request = $context->getRequest();
		if (file_exists($directoryList)) {
			if($this->_request->getFullActionName() == 'sales_order_view'){
				  $buttonList->add(
					'fedpdf_download',
					['label' => __('Fedex Download'), 'onclick' => 'setLocation("'.$location.'")', 'class' => 'reset'],
					-1
				);
			}
		}

    }
}
?>