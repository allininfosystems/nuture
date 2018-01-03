<?php
namespace Bluethink\Refferalcode\Block\Adminhtml\Rewardpoint\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    protected function _construct()
    {
		
        parent::_construct();
        $this->setId('checkmodule_rewardpoint_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Refferalcode'));
    }
}