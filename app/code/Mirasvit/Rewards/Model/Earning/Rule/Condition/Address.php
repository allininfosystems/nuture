<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Rewards\Model\Earning\Rule\Condition;

class Address extends \Magento\SalesRule\Model\Rule\Condition\Address
{
    public function __construct(
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Directory\Model\Config\Source\Country $directoryCountry,
        \Magento\Directory\Model\Config\Source\Allregion $directoryAllregion,
        \Magento\Shipping\Model\Config\Source\Allmethods $shippingAllmethods,
        \Magento\Payment\Model\Config\Source\Allmethods $paymentAllmethods,
        array $data = []
    ) {
        parent::__construct(
            $context, $directoryCountry, $directoryAllregion, $shippingAllmethods, $paymentAllmethods, $data
        );

        $this->taxConfig = $taxConfig;
    }

    /**
     * Validate Address Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $address = $model;

        if (
            'base_subtotal' == $this->getAttribute() &&
            $this->taxConfig->displayCartSubtotalInclTax($address->getQuote()->getStore())
        ) {
            $address->setData($this->getAttribute(), $address->getSubtotalInclTax());
        }

        return parent::validate($address);
    }
}
