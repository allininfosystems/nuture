<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model\System\Config;

class RedirectPage implements \Magento\Framework\Option\ArrayInterface
{

    const ACCOUNT_PAGE = 0;
    const CART_PAGE = 1;
    const HOME_PAGE = 2;
    const CURRENT_PAGE = 3;
    const CUSTOM_PAGE = 4;

    /**
     * get redirect page value.
     *
     * @return []
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ACCOUNT_PAGE,
                'label' => __('Account Page'),
            ],
            [
                'value' => self::CART_PAGE,
                'label' => __('Cart Page'),
            ],
            [
                'value' => self::HOME_PAGE,
                'label' => __('Home Page'),
            ],
            [
                'value' => self::CURRENT_PAGE,
                'label' => __('Current Page'),
            ],
            [
                'value' => self::CUSTOM_PAGE,
                'label' => __('Custom Page'),
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