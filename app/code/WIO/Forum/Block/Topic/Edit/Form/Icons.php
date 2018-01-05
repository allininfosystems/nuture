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

namespace WIO\Forum\Block\Topic\Edit\Form;

class Icons extends \Magento\Framework\View\Element\Template {
  
  protected $_registry;
  protected $_modelIcon;

  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Framework\Registry $registry, 
    \WIO\Forum\Model\Icon $modelIcon,
    array $data = []
  ) {
    parent::__construct($context, $data);
    $this->_registry = $registry;
    $this->_modelIcon = $modelIcon;
  }
  
  protected function _prepareLayout() {
    return parent::_prepareLayout();
  }
  
  public function getAllIcons() {
    return $this->_modelIcon->getForumIcons();
  }
  
  public function getIconPath($iconId) {
    return $this->_modelIcon->getIconFrontImgSmall($iconId);
  }
  
  public function getCurrentTopic() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_TOPIC_OBJECT);
  }
  
  public function getCurrentIcon() {
    $topic = $this->getCurrentTopic();
    if($topic) {
      return $topic->getIconId();
    }
  }
}