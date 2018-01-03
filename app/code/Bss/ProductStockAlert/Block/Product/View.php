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
namespace Bss\ProductStockAlert\Block\Product;

/**
 * Product view stock alerts
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Helper instance
     *
     * @var \Bss\ProductStockAlert\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $coreHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Bss\ProductStockAlert\Helper\Data $helper
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\Helper\PostHelper $coreHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bss\ProductStockAlert\Helper\Data $helper,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\Helper\PostHelper $coreHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->helper = $helper;
    }

    /**
     * Retrieve currently edited product object
     *
     * @return \Magento\Catalog\Model\Product|boolean
     */
    protected function getProduct()
    {
        $product = $this->registry->registry('current_product');
        if ($product && $product->getId()) {
            return $product;
        }
        return false;
    }

    /**
     * Retrieve post action config
     *
     * @return string
     */
    public function getPostAction()
    {
        return $this->getSignupUrl();
    }

    /**
     * Retrieve form action
     *
     * @return string
     */
    public function getAjaxUrl()
    {
        return $this->helper->getAjaxUrl();
    }

    public function checkCustomer()
    {
        return $this->helper->checkCustomer();
    }

    public function getCustomerEmail()
    {
        return $this->helper->getCustomerEmail();
    }

    public function hasEmail($productId)
    {
        return $this->helper->hasEmail($productId);
    }

    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    public function getNotificationMessage()
    {
        return $this->helper->getNotificationMessage();
    }
}
