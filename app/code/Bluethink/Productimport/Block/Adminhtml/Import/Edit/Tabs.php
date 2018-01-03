<?php
namespace Bluethink\Productimport\Block\Adminhtml\Import\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_import_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Import Simple Product'));
    }
}