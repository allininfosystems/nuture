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

class Mode implements \Magento\Framework\Option\ArrayInterface
{
    const FILTER_MODE_AUTO = 0;
    const FILTER_MODE_MANUAL = 1;

    const CONFIG_PATH = 'general/mode';

    /**
     * Options getter
     *
     * @return Array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::FILTER_MODE_AUTO, 'label' => __('Auto Refresh')],
            ['value' => self::FILTER_MODE_MANUAL, 'label' => __('Manual Refresh')],
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
            self::FILTER_MODE_AUTO => __('Auto Refresh'),
            self::FILTER_MODE_MANUAL => __('Manual Refresh'),
        ];
    }
}
