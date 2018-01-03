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
namespace Bss\ProductStockAlert\Controller\Unsubscribe;

use Bss\ProductStockAlert\Controller\Unsubscribe as UnsubscribeController;
use Magento\Framework\Controller\ResultFactory;

class StockAll extends UnsubscribeController
{
    protected $modelStock;

    protected $store;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Bss\ProductStockAlert\Model\Stock $modelStock,
        \Magento\Store\Model\StoreManagerInterface $store
    ) {
        $this->modelStock = $modelStock;
        $this->store = $store;
        parent::__construct($context, $customerSession);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        try {
            $this->modelStock
                ->deleteCustomer(
                    $this->customerSession->getCustomerId(),
                    $this->store
                        ->getStore()
                        ->getWebsiteId()
                );
            $this->messageManager->addSuccess(__('You will no longer receive stock alerts.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Unable to update the alert subscription.'));
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setUrl($this->_url->getUrl("productstockalert"));
    }
}
