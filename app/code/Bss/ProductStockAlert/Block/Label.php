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
namespace Bss\ProductStockAlert\Block;

class Label extends \Magento\Framework\View\Element\Template
{
	protected $helper;

    protected $httpContext;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Bss\ProductStockAlert\Helper\Data $helper,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helper = $helper;
        $this->httpContext = $httpContext;
    }
    
    public function isStockAlertAllowed()
    {
        return $this->helper->isStockAlertAllowed();
    }

    public function checkCustomer()
    {
        $customerGroupId = $this->httpContext->getValue(\Bss\ProductStockAlert\Model\Customer\Context::CONTEXT_CUSTOMER_GROUP_ID);
        return $this->helper->checkCustomer($customerGroupId);
    }
}
