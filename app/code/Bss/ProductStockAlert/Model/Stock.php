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
namespace Bss\ProductStockAlert\Model;

class Stock extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Bss\ProductStockAlert\Model\ResourceModel\Stock');
    }

    /**
     * @return $this
     */
    public function loadByParam()
    {
        if ($this->getProductId() !== null && $this->getCustomerId() !== null && $this->getWebsiteId() !== null) {
            $this->getResource()->loadByParam($this);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function loadByParamGuest()
    {
        if ($this->getProductId() !== null && $this->getCustomerEmail() !== null && $this->getWebsiteId() !== null) {
            $this->getResource()->loadByParamGuest($this);
        }
        return $this;
    }

    /**
     * @param string $customerEmail
     * @param int $websiteId
     * @return $this
     */
    public function deleteCustomer($customerEmail, $websiteId = 0)
    {
        $this->getResource()->deleteCustomer($this, $customerEmail, $websiteId);
        return $this;
    }
}
