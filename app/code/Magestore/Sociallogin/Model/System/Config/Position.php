<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model\System\Config;

class Position implements \Magento\Framework\Option\ArrayInterface
{

    const HEADER_POSITION = 'header';
    const BEFORE_CUSTOMER_LOGIN_POSITION = 'before-customer-login';
    const AFTER_CUSTOMER_LOGIN_POSITION = 'after-customer-login';
    const BEFORE_CUSTOMER_REGISTRATION_POSITION = 'before-customer-registration';
    const AFTER_CUSTOMER_REGISTRATION_POSITION = 'after-customer-registration';
    const POPUP_POSITION = 'popup';

    /**
     * get position value.
     *
     * @return []
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::HEADER_POSITION,
                'label' => __('Header'),
            ],
            [
                'value' => self::BEFORE_CUSTOMER_LOGIN_POSITION,
                'label' => __('Above customer login form'),
            ],
            [
                'value' => self::AFTER_CUSTOMER_LOGIN_POSITION,
                'label' => __('Below customer login form'),
            ],
            [
                'value' => self::BEFORE_CUSTOMER_REGISTRATION_POSITION,
                'label' => __('Above customer registration form'),
            ],
            [
                'value' => self::AFTER_CUSTOMER_REGISTRATION_POSITION,
                'label' => __('Below customer registration form'),
            ],
            [
                'value' => self::POPUP_POSITION,
                'label' => __('Show popup when click login'),
            ],

        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return [
            0 => __('No'),
            1 => __('Yes'),
        ];
    }
}
