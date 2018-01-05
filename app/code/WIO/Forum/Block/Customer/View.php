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

namespace WIO\Forum\Block\Customer;

class View extends \Magento\Framework\View\Element\Template {

  protected $_forumUser;
  protected $_registry;
  protected $_forumData;
  
  public function __construct(
  \Magento\Framework\View\Element\Template\Context $context, 
  \WIO\Forum\Model\User $forumUser, 
  \Magento\Framework\Registry $registry, 
  \WIO\Forum\Helper\Data $forumData,
  array $data = array()
  ) {
    $this->_registry = $registry;
    $this->_forumUser = $forumUser;
    $this->_forumData = $forumData;
    parent::__construct($context, $data);
  }
  
  protected function _prepareLayout() {
      
    $pageLayout = $this->_forumData->getForumPageLayout();
    if ($pageLayout) {
        $this->pageConfig->setPageLayout($pageLayout);
    }  
    parent::_prepareLayout();
  }
  
  protected function getUserId() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_USER_ID_PARAM);
  }
  
  public function getTimeAccordingToTimeZone($dateTime) {
    return $this->_forumData->getTimeAccordingToTimeZone($dateTime);
  }
  
  public function getUserData() {
    $userId = $this->getUserId();
    $userData = $this->_forumUser->getForumUserData($userId);
    return $userData;
  }
}
