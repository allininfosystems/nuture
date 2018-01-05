<?php

namespace WIO\Forum\Block\Adminhtml\Topic\Edit;

/**
 * Admin page left menu
 */
class Tabs extends \Magento\Backend\Block\Widget\Tabs {

  protected function _construct() {
    parent::_construct();
    $this->setId('topic_tabs');
    $this->setDestElementId('edit_form');
    $this->setTitle(__('Topic Information'));
  }

}
