<?php

/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

namespace WIO\Forum\Block\Adminhtml\Adminsettings;

class Edit extends \Magento\Backend\Block\Widget\Form\Container {
  /**
   * Core registry
   *
   * @var \Magento\Framework\Registry
   */
  protected $_coreRegistry = null;

  /**
   * @param \Magento\Backend\Block\Widget\Context $context
   * @param \Magento\Framework\Registry $registry
   * @param array $data
   */
  public function __construct(
    \Magento\Backend\Block\Widget\Context $context, 
    \Magento\Framework\Registry $registry, 
    array $data = []
  ) {
    $this->_coreRegistry = $registry;
    parent::__construct($context, $data);
  }

  /**
   * Initialize cms page edit block
   *
   * @return void
   */
  protected function _construct() {
    $this->_objectId   = 'user_id';
    $this->_blockGroup = 'WIO_Forum';
    $this->_controller = 'adminhtml_adminsettings';

    parent::_construct();

    if ($this->_isAllowedAction('WIO_Forum::forum_save_adminsettings')) {
      $this->buttonList->update('save', 'label', __('Save Settings'));
    } else {
      $this->buttonList->remove('save');
    }
  }

  /**
   *
   * @return \Magento\Framework\Phrase
   */
  public function getHeaderText() {
    return __("Edit Forum Adminsettings");
  }

  /**
   * Check permission for passed action
   *
   * @param string $resourceId
   * @return bool
   */
  protected function _isAllowedAction($resourceId) {
    return $this->_authorization->isAllowed($resourceId);
  }

  /**
   * Prepare layout
   *
   * @return \Magento\Framework\View\Element\AbstractBlock
   */
  protected function _prepareLayout() {
    return parent::_prepareLayout();
  }

}
