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

class Placement implements \Magento\Framework\Option\ArrayInterface
{
    const PLACEMENT_TOOLBAR = 'toolbar';
    const PLACEMENT_SIDEBAR = 'sidebar';

    /**
     * Path to configuration. Configuration section id setted in Data helper
     */
    const CONFIG_PATH = 'general/placement';

    /**
     * Options getter
     *
     * @return Array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::PLACEMENT_TOOLBAR, 'label' => __('Horizontal Layered Navigation')],
            ['value' => self::PLACEMENT_SIDEBAR, 'label' => __('Vertical Layered Navigation')],
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
            self::PLACEMENT_TOOLBAR => __('Horizontal Layered Navigation'),
            self::PLACEMENT_SIDEBAR => __('Vertical Layered Navigation'),
        ];
    }
}
