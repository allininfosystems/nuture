<?php
namespace Bluethink\Dailydose\Block\Adminhtml\Brand\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_brand_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Brand'));
    }
}