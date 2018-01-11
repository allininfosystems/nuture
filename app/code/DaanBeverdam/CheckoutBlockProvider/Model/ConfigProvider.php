<?php

namespace DaanBeverdam\CheckoutBlockProvider\Model;


use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;

class ConfigProvider implements ConfigProviderInterface
{
    /** @var LayoutInterface  */
    protected $_layout;
    protected $cmsBlock;

    public function __construct(LayoutInterface $layout, $blockId)
    {
        $this->_layout = $layout;
        $this->cmsBlock = $this->constructBlock($blockId);
    }

    public function constructBlock($blockId){
		$zip='';
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
        $block = $html;
        return $block;
    }

    public function getConfig()
    {
        return [
            'cms_block' => $this->cmsBlock
        ];
    }
}