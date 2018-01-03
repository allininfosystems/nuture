<?php

namespace Cminds\StockNotification\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StockNotification extends AbstractDb
{
    /**
     * Resource model initialization.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('cminds_stocknotification_request', 'id');
    }
}
