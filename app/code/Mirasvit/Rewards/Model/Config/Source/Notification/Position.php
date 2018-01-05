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



namespace Mirasvit\Rewards\Model\Config\Source\Notification;

use Mirasvit\Rewards\Model\Config as Config;

class Position implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toArray()
    {
        return [
            Config::NOTIFICATION_POSITION_ACCOUNT_REWARDS => __('Customer Account > My Reward Points'),
            Config::NOTIFICATION_POSITION_ACCOUNT_REFERRALS => __('Customer Account > My Referrals'),
            Config::NOTIFICATION_POSITION_CART => __('Cart Page'),
        //            Config::NOTIFICATION_POSITION_CHECKOUT => __('Checkout Page'),
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = [];
        foreach ($this->toArray() as $k => $v) {
            $result[] = ['value' => $k, 'label' => $v];
        }

        return $result;
    }

    /************************/
}
