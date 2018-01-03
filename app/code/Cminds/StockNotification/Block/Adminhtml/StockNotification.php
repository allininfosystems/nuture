<?php

namespace Cminds\StockNotification\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class StockNotification extends Container
{
    /**
     * constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_request';
        $this->_blockGroup = 'Cminds_StockNotification';
        $this->_headerText = __('Customer Request');

        parent::_construct();

        $this->removeButton('add');
    }
}
