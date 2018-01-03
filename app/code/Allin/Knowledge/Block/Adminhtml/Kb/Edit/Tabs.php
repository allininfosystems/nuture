<?php
namespace Allin\Knowledge\Block\Adminhtml\Kb\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('kb_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Comment Information'));
    }
}