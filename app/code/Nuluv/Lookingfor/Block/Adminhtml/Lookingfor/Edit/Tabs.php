<?php
namespace Nuluv\Lookingfor\Block\Adminhtml\Lookingfor\Edit;

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
        $this->setId('lookingfor_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Lookingfor Information'));
    }
}