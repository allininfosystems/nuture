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

namespace WIO\Forum\Block\Adminhtml\Topic\Edit\Tab;

class Icon extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface {

  protected $_modelIcon;

  public function __construct(
  \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \WIO\Forum\Model\Icon $modelIcon, array $data = []
  ) {
    $this->_coreRegistry = $registry;
    $this->_modelIcon = $modelIcon;
    parent::__construct($context, $data);
  }

  public function getCurrentIcon() {
    $modelTopic = $this->_coreRegistry->registry('topic_model');
    return $modelTopic->getIconId();
  }

  public function getAllIcons() {
    return $this->_modelIcon->getForumIcons();
  }

  public function getIconPath() {
    return $this->_modelIcon->getAdminhtmlIconPath();
  }

  /**
   * Prepare label for tab
   *
   * @return \Magento\Framework\Phrase
   */
  public function getTabLabel() {
    return __('Topic Icons');
  }

  /**
   * Prepare title for tab
   *
   * @return \Magento\Framework\Phrase
   */
  public function getTabTitle() {
    return __('Topic Icons');
  }

  /**
   * {@inheritdoc}
   */
  public function canShowTab() {
    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function isHidden() {
    return false;
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

}
