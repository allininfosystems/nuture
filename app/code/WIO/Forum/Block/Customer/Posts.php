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

class Posts extends \Magento\Framework\View\Element\Template {

  protected $_forumData;
  protected $_registry;
  protected $_helperUrl;
  protected $_postsModel;
  protected $_topicModel;
  
  protected $_collection;
  protected $_topicsLoaded;
  protected $_moderatorModel;
  
  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, 
    \WIO\Forum\Helper\Data $forumData, 
    \WIO\Forum\Helper\Url $helperUrl, 
    \WIO\Forum\Model\PostFactory $postsModel, 
    \WIO\Forum\Model\TopicFactory $topicModel, 
    \Magento\Framework\Registry $registry,   
    \WIO\Forum\Model\Moderator $moderatorModel,
    array $data = array()
  ) {
    $this->_forumData = $forumData;
    $this->_registry = $registry;
    $this->_helperUrl = $helperUrl;
    $this->_postsModel = $postsModel;
    $this->_topicModel = $topicModel;
    $this->_moderatorModel = $moderatorModel;
  
    parent::__construct($context, $data);
  }

  protected function _prepareLayout() {
    $this->_collection = $this->initCollection();
    parent::_prepareLayout();
    if(!$this->_collection) {
      return;
    }
    $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager', 'wioforum.forum.pager'
    );
    $pager->setLimit($this->getPageLimit())
            ->setShowAmounts(true)
            ->setCollection($this->_collection);
    $pager->setAvailableLimit(array(10=>10, 20=>20, 30=>30, 50=>50));
    $this->setChild('pager', $pager);

    return $this;
  }
  
  public function getPagerHtml() {
    return $this->getChildHtml('pager');
  }
  
  public function getForumUrl() {
    return $this->_helperUrl->getForumUrl();
  }

  public function getSortType() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_SORT_KEY_REGISTER);
  }

  protected function getSortField() {
    return \WIO\Forum\Helper\Constant::WIO_FORUM_CREATED_TIME_SORT_FIELD;
  }

  protected function getPageNum() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_PAGE_KEY_REGISTER);
  }

  protected function getPageLimit() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_LIMIT_KEY_REGISTER);
  }

  public function getTimeAccordingToTimeZone($dateTime) {
    return $this->_forumData->getTimeAccordingToTimeZone($dateTime);
  }
  
  public function getCollection() {
    return $this->_collection;
  }
  
  public function getParentTopic($post) {
    $parentId = $post->getParentId();
    if(!empty($this->_topicsLoaded[$parentId])) {
      return $this->_topicsLoaded[$parentId];
    }
    $this->_topicsLoaded[$parentId] = $this->_topicModel->create()->load($parentId);
    return $this->_topicsLoaded[$parentId];
  }
  
  public function getDeletePostLink($_post){
    return $this->_helperUrl->getDeletePostUrl($_post->getId());
  }
  
  public function getEditPostLink($_post){
    $parentTopic = $this->getParentTopic($_post);
    return $this->_helperUrl->getEditTopicUrl($parentTopic->getParentId(), $parentTopic->getId(), $_post->getId());
  }
  
  public function getViewPostLink($_post){
    return $this->_helperUrl->getLatestViewUrl($_post->getId());
  }
  
  protected function getCustomer() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION );
  }
  
  public function getIsModerator() {
    if($customer = $this->getCustomer()) {
      return $this->_moderatorModel->isModerator($customer->getId());
    }
  }

  protected function initCollection() {
    $collection = $this->_postsModel->create()->getCollection();
    if(!$this->getCustomer() || !$collection) {
      return;
    }
    $collection->setCurPage($this->getPageNum());
    if(!$this->getIsModerator()) {
      $collection->getUserPosts($this->getCustomer()->getId());
    }else{
      $collection->enabledOnly()
              ->notDeleted();
    }        
    $collection->setOrder($this->getSortField(), $this->getSortType());
    return $collection;
  }
  
  public function getSortUrl($type = 'asc'){
    return $this->getUrl('*/*/*', array(
      \WIO\Forum\Helper\Constant::WIO_FORUM_SORTING => $type
    ));
  }
}
