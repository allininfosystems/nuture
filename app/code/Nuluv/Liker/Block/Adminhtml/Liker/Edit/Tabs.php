<?php
namespace Nuluv\Liker\Block\Adminhtml\Liker\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_liker_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Liker Information'));
    }
}