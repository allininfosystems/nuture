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

class Rss extends \Magento\Framework\View\Element\Template {

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
    return $this->_forumData->getIsRssEnabled();
  }
  
  public function getLinkUrl() {
    $params = array();
    $currentForum = $this->getCurrentForum();
    $currentTopic = $this->getCurrentTopic();
    if($currentForum && $currentForum->getId()) {
      $params[\WIO\Forum\Helper\Constant::WIO_FORUM_ID_PARAM_NAME] = $currentForum->getId();
    }
    if($currentTopic && $currentTopic->getId()) {
      $params[\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME] = $currentTopic->getId();
    }
    return $this->getUrl(\WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/rss/index', $params);
  }
  
  protected function getCurrentTopic(){
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_TOPIC);
  }
  
  protected function getCurrentForum() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_FORUM);
  }
}
