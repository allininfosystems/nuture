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

class AdditionalFilters
{
    const FILTER_RATING = 'rating';
    const FILTER_STOCK_STATUS = 'stock_status';

    const STOCK_STATUS_FILTER_CLASS = 'Plumrocket\ProductFilter\Model\Layer\Filter\StockStatus';
    const RATING_FILTER_CLASS = 'Plumrocket\ProductFilter\Model\Layer\Filter\Rating';
    const CUSTOM_OPTION_FILTER_CLASS = 'Plumrocket\ProductFilter\Model\Layer\Filter\CustomOption';

    /**
     * Get options in "key-value" format
     *
     * @return Array
     */
    public function toArray()
    {
        return [
            self::FILTER_RATING => __('Rating'),
            self::FILTER_STOCK_STATUS => __('Stock Status'),
            \Magento\Catalog\Model\Layer\FilterList::CATEGORY_FILTER => __('Categories'),
        ];
    }
}
