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

class Cart extends \Magento\Rule\Model\Condition\AbstractCondition
{
    const OPTION_REWARDS_POINTS_USED = 'rewards_points_used';
    const OPTION_COUPON_USED = 'coupon_used';
    const OPTION_COUPON_CODE = 'coupon_code';

    public function __construct(
        \Mirasvit\Rewards\Helper\Purchase $rewardsPurchase,
        \Magento\Rule\Model\Condition\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->rewardsPurchase = $rewardsPurchase;
    }

    /**
     * Load attribute options
     *
     * @return $this
     */
    public function loadAttributeOptions()
    {
        $attributes = [
            self::OPTION_REWARDS_POINTS_USED => __('Is Rewards Points used'),
            self::OPTION_COUPON_USED         => __('Coupon Used'),
            self::OPTION_COUPON_CODE         => __('Coupon Code'),
        ];

        $this->setAttributeOption($attributes);

        return $this;
    }

    /**
     * Get attribute element
     *
     * @return $this
     */
    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    /**
     * Get input type
     *
     * @return string
     */
    public function getInputType()
    {
        $type = 'string';

        switch ($this->getAttribute()) {
            case self::OPTION_REWARDS_POINTS_USED:
            case self::OPTION_COUPON_USED:
                $type = 'select';
                break;
        }

        return $type;
    }

    /**
     * Get value element type
     *
     * @return string
     */
    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case self::OPTION_REWARDS_POINTS_USED:
            case self::OPTION_COUPON_USED:
                return 'select';
        }
        return 'text';
    }

    /**
     * Get value select options
     *
     * @return array|null
     */
    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case self::OPTION_REWARDS_POINTS_USED:
                case self::OPTION_COUPON_USED:
                    $options = [
                        ['value' => 0, 'label' => __('No')],
                        ['value' => 1, 'label' => __('Yes')],
                    ];
                    break;
                default:
                    $options = [];
            }
            $this->setData('value_select_options', $options);
        }
        return $this->getData('value_select_options');
    }

    /**
     * Validate Cart Rule Condition
     *
     * @param \Magento\Framework\Model\AbstractModel $model
     * @return bool
     */
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $model;
        if (!$address instanceof \Magento\Quote\Model\Quote\Address) {
            if ($model->getQuote()->isVirtual()) {
                $address = $model->getQuote()->getBillingAddress();
            } else {
                $address = $model->getQuote()->getShippingAddress();
            }
        }

        $purchase = $this->rewardsPurchase->getByQuote($address->getQuote());
        if (self::OPTION_REWARDS_POINTS_USED == $this->getAttribute()) {
            $address->setData(self::OPTION_REWARDS_POINTS_USED, (int)(bool)$purchase->getSpendAmount());
        }
        $totals = $address->getTotals();
        if ($totals) {
            $totalKeys  = array_keys($totals);
            $couponUsed = 0;
            if (in_array('discount', $totalKeys) && !in_array('rewards_spend', $totalKeys)) {
                $couponUsed = 1;
            }

            $address->setData(self::OPTION_COUPON_USED, $couponUsed);
            $address->setData(self::OPTION_COUPON_CODE, $address->getQuote()->getCouponCode());
        }

        return parent::validate($address);
    }
}
