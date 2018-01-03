<?php
namespace Bluethink\Dailydose\Block\Adminhtml\Dailydose\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_dailydose_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Dailydose'));
    }
}