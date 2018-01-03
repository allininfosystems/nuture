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
namespace Bss\ProductStockAlert\Block\Product\View\Type;

class Grouped extends \Magento\GroupedProduct\Block\Product\View\Type\Grouped
{
    /**
     * Helper instance
     *
     * @var \Bss\ProductStockAlert\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Bss\ProductStockAlert\Helper\Data $helper,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct(
            $context,
            $arrayUtils,
            $data            
        );
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

    public function getPostAction($product_id)
    {
        return $this->helper->getPostAction($product_id);
    }

    public function isStockAlertAllowed()
    {
        return $this->helper->isStockAlertAllowed();
    }
}
