<?php
namespace Nuluv\Pincode\Block\Adminhtml\Pincode\Edit;

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
        $this->setId('pincode_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Pincode Information'));
    }
}