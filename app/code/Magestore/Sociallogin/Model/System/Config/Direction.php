<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Model\System\Config;

class Direction implements \Magento\Framework\Option\ArrayInterface
{

    const LEFT_DIRECTION = 'left';
    const RIGHT_DIRECTION = 'right';

    /**
     * get direction value.
     *
     * @return []
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::LEFT_DIRECTION,
                'label' => __('Left to Right'),
            ],
            [
                'value' => self::RIGHT_DIRECTION,
                'label' => __('Right to Left'),
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