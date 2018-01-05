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

namespace WIO\Forum\Block\Forum\View;

class ListTopic extends \WIO\Forum\Block\Forum\View {
  
  protected $_collection = null;

  protected function _prepareLayout() {
    $this->_collection = $this->getAllTopics();
    parent::_prepareLayout();
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

  public function getIsIconsEnabled() {
    return $this->_forumData->getIsIconsEnabled();
  }
  
  public function getIconSrc($model = NULL, $notSmall = false){
    $_model = $model ? $model : $this->getParentForum();
    if(!$notSmall){
      return $this->_icon->getIconFrontImgSmall($_model->getIconId()); 
    }
    return $this->_icon->getIconFrontImgBig($_model->getIconId());
  }
  
  public function getTopicViewUrl($topicObj) {
    return $this->_helperUrl->getTopicUrl($this->getParentForum(), $topicObj);
  }
   
  public function getForumTitle(){
    return $this->getParentForum()
            ->getTitle();
  }
  
  public function getDisplayTopics(){
    $this->_collection->setPageSize($this->getPageLimit());
    $this->_collection->setCurPage($this->getPageNum());
    return $this->_collection; 
  }
  
  public function getSortUrl($forum = null, $type = 'asc'){
    if($forum == null){
      $forum = $this->getParentForum();
    }
    return $this->_helperUrl->getForumUrl($forum, array(
        \WIO\Forum\Helper\Constant::WIO_FORUM_SORTING => $type,
        \WIO\Forum\Helper\Constant::WIO_FORUM_PAGE_NUM => 1
    ));
  }
  
  public function getTopicCreatorName($topicModel) {
    return $this->_forumUser->getForumUserName($topicModel->getSystemUserId());
  }

  public function getTopicCreatorLink($topicModel) {
    return $this->_forumUser->getUserLink($topicModel->getSystemUserId());
  }
  
  public function getAddNewUrl() {
    $parentForum = $this->getParentForum();
    return $this->_helperUrl->getAddTopicUrl($parentForum);
  }
  
  public function getIsOwner($topicModel) {
    
    if($this->getIsModerator()) {
      return true;
    }
    $customer = $this->getCustomer();
    if($customer) {
      if($topicModel->getSystemUserId() == $customer->getId()) {
        return true;
      }
    }
     
  }
  
  public function getEditLink($topicModel) {
    $parentForum = $this->getParentForum();
    $post_id = $this->getMyFirstPostId($topicModel);
    return $this->_helperUrl->getEditTopicUrl($parentForum->getId(), $topicModel->getId(), $post_id);
  }
  
  public function getDeleteLink($topicModel) {
    return $this->_helperUrl->getDeleteTopicLink($topicModel->getId());
  }
  
  public function getIsCustomerAllowedDeleteTopics() {
    return $this->_forumData->getIsCustomerAllowedDeleteTopics();
  }
  
  /**** LATEST ****/
  
  public function getLatestPostDetails($_postId) {
    return $this->_latestModel->getLatestDetails($_postId);
  }
  
  public function getLatestTopicTitle($latest){
    $topic = $latest['parent_topic'];
    return $topic->getTitle();
  }
  
  public function getLatestIcon($latest) {
    $topic = $latest['parent_topic'];
    if($this->getIconSrc($topic)) {
      return $this->getIconSrc($topic);
    }
  }
  public function getLatestViewUrl($latest) {
    $post = $latest['post'];
    return $this->_helperUrl->getLatestViewUrl($post->getId());
  }
  
  public function getLatestPostedDate($latest){
    $post = $latest['post'];
    return $this->getTimeAccordingToTimeZone($post->getCreatedTime());
  }
  
  public function getLatestPostedBy($latest){
    $post = $latest['post'];
    return $this->_forumUser->getForumUserName($post->getSystemUserId());
  }
  
  public function getLatestPostedByUrl($latest){
    $post = $latest['post'];
    return $this->_forumUser->getUserLink($post->getSystemUserId());
  }
  
  public function getLatestTopicDescription($latest){
    $topic = $latest['parent_topic'];
    return $topic->getDescription();
  }
  
  
  protected function getMyFirstPostId($topicModel){
    if($this->getIsModerator()) {
      $customerId = $topicModel->getSystemUserId();
    }else{
      $customer = $this->getCustomer();
      if($customer) {
        $customerId = $this->getCustomer()->getId();
      }
    }
    if(!$customerId) {
      return;
    }
    $postsCollection = $this->_postModel->create()->getCollection();
    $postsCollection->getUserPosts($customerId)
            ->byParent($topicModel->getId())
            ->setOrder($this->getSortField(), 'asc');
    if($postsCollection->getSize()) {
      $itemFirst = $postsCollection->getFirstItem();
      return $itemFirst->getId();
    }
  }
}