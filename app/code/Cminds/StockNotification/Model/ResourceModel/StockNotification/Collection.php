<?php

namespace Cminds\StockNotification\Model\ResourceModel\StockNotification;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Collection initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Cminds\StockNotification\Model\StockNotification::class,
            \Cminds\StockNotification\Model\ResourceModel\StockNotification::class
        );
    }
}
