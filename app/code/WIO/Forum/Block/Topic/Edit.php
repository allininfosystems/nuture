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

namespace WIO\Forum\Block\Topic;

class Edit extends \Magento\Framework\View\Element\Template {
  
  protected $_registry;
  protected $_forumData;
  protected $_moderatorModel;

  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Framework\Registry $registry, 
    \WIO\Forum\Helper\Data $forumData,  
    \WIO\Forum\Model\Moderator $moderatorModel,    
    array $data = []
  ) {
    parent::__construct($context, $data);
    $this->_registry = $registry;
    $this->_forumData = $forumData;
    $this->_moderatorModel = $moderatorModel;
  }
  
  protected function _prepareLayout() {
    $this->pageConfig->getTitle()->set( __('Forum Add / Edit / Topic / Post'));
    
    $pageLayout = $this->_forumData->getForumPageLayout();
    if ($pageLayout) {
        $this->pageConfig->setPageLayout($pageLayout);
    }  
    return parent::_prepareLayout();
  }
  
  
  public function getIsModerator() {
    if($customer = $this->getCurrentCustomerSession()) {
      return $this->_moderatorModel->isModerator($customer->getId());
    }
  }
  
  public function getFormTitle(){
    $topicObj = $this->getEditTopic();
    if(!$topicObj) {
      return __('Add New Topic');
    }
  }
  
  public function getEditForum() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_FORUM_OBJECT);
  }
  
  public function getEditTopic() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_TOPIC_OBJECT);
  }
  
  public function getEditPost(){
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_POST_OBJECT);
  }
  
  public function getCurrentCustomerSession() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION);
  }
  
  public function getIsOwner($object) {
    if($this->getIsModerator()) {
      return true;
    }
    $customerSession = $this->getCurrentCustomerSession();
    
    if($object->getSystemUserId() == $customerSession->getId()) {
      return true;
    }
  }
  
}
