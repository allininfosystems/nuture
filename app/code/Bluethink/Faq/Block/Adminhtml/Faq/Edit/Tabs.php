<?php
namespace Bluethink\Faq\Block\Adminhtml\Faq\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_faq_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Faq Information'));
    }
}