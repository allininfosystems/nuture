<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_ProductStockAlert
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ProductStockAlert\Controller\Ajax;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Index extends \Magento\Framework\App\Action\Action
{
	protected $resultPageFactory = false;

    protected $jsonHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        JsonHelper $jsonHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
    }

    
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var \Magento\Framework\View\Result\Layout $resultLayout */
        $resultPage = $this->resultPageFactory->create();
        if($data['statusAvailable'] == "grouped") {
            $data['statusAvailable'] = 0;
            $result['html'] = $resultPage->getLayout()
                ->createBlock('Bss\ProductStockAlert\Block\Product\View')
                ->setAction($data['action'])
                ->setProductIdController($data['productId'])
                ->setStatusAvailable($data['statusAvailable'])
                ->setTemplate('Bss_ProductStockAlert::product/view/type/grouped/form.phtml')
                ->toHtml();
        }else {
            $result['html'] = $resultPage->getLayout()
                ->createBlock('Bss\ProductStockAlert\Block\Product\View')
                ->setAction($data['action'])
                ->setProductIdController($data['productId'])
                ->setStatusAvailable($data['statusAvailable'])
                ->setTemplate('Bss_ProductStockAlert::product/view/form.phtml')
                ->toHtml();
        }
        return $this->getResponse()->setBody($this->jsonHelper->jsonEncode($result));
    }
}