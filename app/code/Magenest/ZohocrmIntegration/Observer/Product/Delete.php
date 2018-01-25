<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ZohocrmIntegration\Observer\Product;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\ZohocrmIntegration\Model\Data as Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use \Magento\Store\Model\ScopeInterface;
use Magenest\ZohocrmIntegration\Model\Sync\Product;

/**
 * Class Delete Product Observer
 */
class Delete implements ObserverInterface
{
    /**
     * Core Config Data
     *
     * @var $_scopeConfig \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magenest\ZohocrmIntegration\Model\Sync\Product
     */
    protected $_product;


    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Product $product
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Product $product
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_product     = $product;
    }

    /**
     * Admin delete product
     *
     * @param  Observer $observer
     * @return string|void
     */
    public function execute(Observer $observer)
    {
        if (!$this->_scopeConfig->isSetFlag(Data::XML_PATH_ALLOW_SYNC_PRODUCT, ScopeInterface::SCOPE_STORE)) {
            return;
        }

        try {
            $product = $observer->getEvent()->getProduct();
            $sku     = $product->getSku();
            if ($sku) {
                $this->_product->delete($sku);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
