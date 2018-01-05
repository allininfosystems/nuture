<?php

namespace WIO\Forum\Block\Adminhtml\Adminsettings\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs {

  protected function _construct() {
    parent::_construct();
    $this->setId('forum_adminsettings_tabs');
    $this->setDestElementId('edit_form');
    $this->setTitle(__('Admin profile information'));
  }

}
