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

class Index extends \Magento\Framework\View\Element\Template {
  
  protected $_registry;
  protected $_forumUser;
  protected $_forumUserModelLoaded;
  
  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, 
    \Magento\Framework\Registry $registry, 
    \WIO\Forum\Model\User $forumUser,
    array $data = array()
  ) {
    $this->_forumUser = $forumUser;
    $this->_registry = $registry;
    parent::__construct($context, $data);
  }
  
  protected function _prepareLayout() {
   parent::_prepareLayout();
  }
  
  public function getFormAction(){
    return $this->getUrl(\WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/customer/save');
  }
  
  public function getNickName(){
    $forumUser = $this->getCustomerSession();
    if($forumUser) {
      return $forumUser['nickname'];
    }
  }
  
  public function getSignature(){
    $forumUser = $this->getCustomerSession();
    if($forumUser) {
      return $forumUser['signature'];
    }
  }
  
  public function getAvatarImg(){
    $forumUser = $this->getCustomerSession();
    if($forumUser) {
      return $forumUser['avatar'];
    }
  }
  
  public function getAvatar() {
    $forumUser = $this->getCustomerSession();
    if($forumUser) {
      return $forumUser['avatar_real'];
    }
  }
  
  protected function getCustomerSession() {
    if($this->_forumUserModelLoaded) {
      return $this->_forumUserModelLoaded;
    }
    $customerSession = $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION);
    if($customerSession) {
      $this->_forumUserModelLoaded = $this->_forumUser->getForumUserData($customerSession->getId());
      return $this->_forumUserModelLoaded;
    }
  }
  
}

