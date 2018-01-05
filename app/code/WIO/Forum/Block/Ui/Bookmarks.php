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

namespace WIO\Forum\Block\Ui;

class Bookmarks extends \Magento\Framework\View\Element\Template {

  protected $_forumData;
  protected $_registry;
  
  public function __construct(
  \Magento\Framework\View\Element\Template\Context $context, 
  \WIO\Forum\Helper\Data $forumData, 
  \Magento\Framework\Registry $registry,
  array $data = []
  ) {
    parent::__construct($context, $data);
    $this->_forumData = $forumData;
    $this->_registry = $registry;
  }

  protected function _prepareLayout() {
    parent::_prepareLayout();
    return $this;
  }

  public function getIsEnabled() {
    return $this->_forumData->getIsBookmarksEnabled();
  }

  public function getIsShowAddButton() {
    $currTopic = $this->getCurrentTopic();
    if($currTopic && $currTopic->getId()) {
      return true;
    }
  }
  
  public function getTopicId() {
    $currTopic = $this->getCurrentTopic();
    if($currTopic && $currTopic->getId()) {
      return $currTopic->getId();
    }
  }
  
  public function getActionForm(){
    return $this->getUrl(\WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/bookmark/index');
  }
  
  protected function getCurrentTopic() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_TOPIC);
  }
}
