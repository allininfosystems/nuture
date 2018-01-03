<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Product Filter v3.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ProductFilter\Model\Config\Source;

class PriceDisplay implements \Magento\Framework\Option\ArrayInterface
{

    const PRICE_DISPLAY_RANGE = 0;
    const PRICE_DISPLAY_INPUT = 1;
    const PRICE_DISPLAY_SLIDER = 2;

    const CONFIG_PATH = 'settings/price_display';

    /**
     * Options getter
     *
     * @return Array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PRICE_DISPLAY_RANGE, 'label' => __('Range')],
            ['value' => self::PRICE_DISPLAY_INPUT, 'label' => __('Input Fields (From - To)')],
            ['value' => self::PRICE_DISPLAY_SLIDER, 'label' => __('Slider (From - To)')],
        ];
    }

    /**
     * Get options in "key-value" format
     *
     * @return Array
     */
    public function toArray()
    {
        return [
            self::PRICE_DISPLAY_RANGE => __('Range'),
            self::PRICE_DISPLAY_INPUT => __('Input Fields (From - To)'),
            self::PRICE_DISPLAY_SLIDER => __('Slider (From - To)'),
        ];
    }
}
