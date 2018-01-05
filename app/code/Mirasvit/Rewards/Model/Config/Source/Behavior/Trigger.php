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



namespace Mirasvit\Rewards\Model\Config\Source\Behavior;

use Mirasvit\Rewards\Model\Config as Config;

class Trigger implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        \Mirasvit\Rewards\Model\Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $triggers = [
            Config::BEHAVIOR_TRIGGER_SIGNUP            => __('Customer signs up in store'),
            Config::BEHAVIOR_TRIGGER_CUSTOMER_ORDER    => __('Customer places order'),
            Config::BEHAVIOR_TRIGGER_SEND_LINK         => __('Customer refer a friend'),
            Config::BEHAVIOR_TRIGGER_TAG               => __('Customer adds tag to a product'),
            Config::BEHAVIOR_TRIGGER_REVIEW            => __('Customer writes a product review'),
            Config::BEHAVIOR_TRIGGER_BIRTHDAY          => __('Customer has a birthday'),
            Config::BEHAVIOR_TRIGGER_INACTIVITY        => __('Customer is inactive for long time'),
            Config::BEHAVIOR_TRIGGER_NEWSLETTER_SIGNUP => __('Newsletter sign up'),
            Config::BEHAVIOR_TRIGGER_FACEBOOK_SHARE    => __('Facebook Share'),
            Config::BEHAVIOR_TRIGGER_FACEBOOK_LIKE     => __('Facebook Like'),
            Config::BEHAVIOR_TRIGGER_TWITTER_TWEET     => __('Twitter Tweet'),
            Config::BEHAVIOR_TRIGGER_GOOGLEPLUS_ONE    => __('Google+ Like'),
            Config::BEHAVIOR_TRIGGER_PINTEREST_PIN     => __('Pinterest Pin'),
            Config::BEHAVIOR_TRIGGER_PUSHNOTIFICATION_SIGNUP  => __('Push Notifications sign up'),
            Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_SIGNUP => __('Referred customer signs up in store'),
            Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER  => __('Order from referred customer'),
        ];

        $customRules = $this->config->getAdvancedSettingsCustomRuleList();
        foreach ($customRules as $rule) {
            $triggers[$rule['code']] = $rule['name'];
        }

        return $triggers;
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
