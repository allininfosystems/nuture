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

namespace WIO\Forum\Block\Forum;

class View extends \Magento\Framework\View\Element\Template {
  
  protected $_topicModelFactory;
  protected $_postModel;
  protected $_forumData;
  protected $_icon;
  protected $_registry;
  protected $_helperUrl;
  protected $_forumUser;
  protected $_latestModel;
  protected $_moderatorModel;
  protected $_isModerator;

  public function __construct(
  \Magento\Framework\View\Element\Template\Context $context, 
  \WIO\Forum\Model\ResourceModel\Topic\CollectionFactory $topicModelFactory, 
  \WIO\Forum\Model\PostFactory $postModel,
  \WIO\Forum\Helper\Data $forumData, 
  \WIO\Forum\Helper\Url $helperUrl, 
  \WIO\Forum\Model\Icon $icon, 
  \Magento\Framework\Registry $registry, 
  \WIO\Forum\Model\User $forumUser,
  \WIO\Forum\Model\Latest $latestModel,
  \WIO\Forum\Model\Moderator $moderatorModel,
  array $data = []
  ) {
    parent::__construct($context, $data);
    $this->_topicModelFactory = $topicModelFactory;
    $this->_forumData = $forumData;
    $this->_icon = $icon;
    $this->_registry = $registry;
    $this->_helperUrl = $helperUrl;
    $this->_forumUser = $forumUser;
    $this->_latestModel = $latestModel;
    $this->_postModel = $postModel;
    $this->_moderatorModel = $moderatorModel;
    $this->setIsmoderator();
  }
  
  protected function _prepareLayout() {
    $keywordsSet = false;
    $descSet = false;
    $pageLayout = $this->_forumData->getForumPageLayout();
    if ($pageLayout) {
        $this->pageConfig->setPageLayout($pageLayout);
    }  
      
    $this->pageConfig->getTitle()->set( __('%1', $this->getParentForum()->getTitle()));
    
    if($this->getParentForum() && $this->getParentForum()->getMetaDescription()) {
        $descSet = true;
        $this->pageConfig->setDescription($this->getParentForum()->getMetaDescription()); //->set($this->getParentForum()->getMetaDescription());
    }
    
    if($this->getParentForum() && $this->getParentForum()->getMetaKeywords()) {
        $keywordsSet = true;
        $this->pageConfig->setKeywords($this->getParentForum()->getMetaKeywords()); //->set($this->getParentForum()->getMetaDescription());
    }
    
    $defaultMetaDesc = $this->_forumData->getForumDefaultDesc();
    if($defaultMetaDesc && !$descSet) {
        $this->pageConfig->setDescription($defaultMetaDesc);
    }
    $defaultMetaKeys = $this->_forumData->getForumDefaultKeys();
    if($defaultMetaKeys && !$keywordsSet) {
        $this->pageConfig->setKeywords($defaultMetaKeys);
    }
    
    $this->_forumData->setLayoutUpdated();
    return parent::_prepareLayout();
  }
  
  
  protected function setIsmoderator() {
    if($customer = $this->getCustomer()) {
      $this->_isModerator = $this->_moderatorModel->isModerator($customer->getId());
    }
  }
  
  public function getIsModerator() {
    return $this->_isModerator;
  }
  
  public function getAllTopics() {
    return $this->_topicModelFactory->create()
            ->byParent($this->getForumId())
            ->topicsOnly()
            ->enabledOnly()
            ->setOrder($this->getSortField(), $this->getSortType())
            ->setCurPage($this->getPageNum());
  }
  
  public function getTimeAccordingToTimeZone($dateTime) {
    return $this->_forumData->getTimeAccordingToTimeZone($dateTime);
  }
  
  protected function getParentForum() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_FORUM);
  }
  
  protected function getForumId(){
    return $this->getParentForum()
            ->getId();
  }
  
  public function getSortType() {
    return  $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_SORT_KEY_REGISTER);
  }
  
  protected function getSortField() {
    return \WIO\Forum\Helper\Constant::WIO_FORUM_CREATED_TIME_SORT_FIELD;
  }
  
  protected function getPageNum() {
    return  $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_PAGE_KEY_REGISTER);
  }
  
  protected function getPageLimit() {
    return  $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_LIMIT_KEY_REGISTER);
  }

  protected function getCustomer() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION);
  }
}