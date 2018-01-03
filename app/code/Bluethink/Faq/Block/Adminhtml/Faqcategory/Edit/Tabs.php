<?php
namespace Bluethink\Faq\Block\Adminhtml\Faqcategory\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_faqcategory_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Faq Category Information'));
    }
}