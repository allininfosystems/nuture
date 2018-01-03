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
namespace Bss\ProductStockAlert\Block\Product\View\Type\Bundle\Option;

class Multi extends \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle\Option\Multi
{
    /**
     * @var string
     */
    protected $_template = 'Bss_ProductStockAlert::product/view/type/bundle/option/multi.phtml';

    /**
     * Helper instance
     *
     * @var \Bss\ProductStockAlert\Helper\Data
     */
    protected $helper;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Checkout\Helper\Cart $cartHelper,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Pricing\Helper\Data $pricingHelper,
        \Bss\ProductStockAlert\Helper\Data $helper,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct(
            $context,
            $jsonEncoder,
            $catalogData,
            $registry,
            $string,
            $mathRandom,
            $cartHelper,
            $taxData,
            $pricingHelper,
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
