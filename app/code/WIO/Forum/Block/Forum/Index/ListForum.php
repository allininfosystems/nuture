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

namespace WIO\Forum\Block\Forum\Index;
use WIO\Forum\Block\Topic\Index\ListPosts;
class ListForum extends \WIO\Forum\Block\Forum\Index {
  
   /* protected $_collection forum */
  protected $_loadedUsers;
  
  protected $_collection = null;

  protected function _prepareLayout() {
    parent::_prepareLayout();
    $this->_collection = $this->getAllForums();
    /** @var \Magento\Theme\Block\Html\Pager */
    $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager', 'wioforum.forum.pager'
    );
    $pager->setLimit($this->getPageLimit())
            ->setShowAmounts(true)
            ->setCollection($this->_collection);
    $pager->setAvailableLimit(array(5=>5,10=>10,30=>30,50=>50));
    $this->setChild('pager', $pager);

    return $this;
  }

  public function getPagerHtml() {
    return $this->getChildHtml('pager');
  }

  public function getForumViewUrl($forumObj) {
    return $this->_helperUrl->getForumUrl($forumObj);
  }

  public function getForumCreatorName($forumObj) {
    return $this->_forumUser->getForumUserName($forumObj->getSystemUserId());
  }

  public function getForumCreatorLink($forumObj) {
    return $this->_forumUser->getUserLink($forumObj->getSystemUserId());
  }

  public function getIsIconsEnabled() {
    return $this->_forumData->getIsIconsEnabled();
  }

  public function getIcon($forumObj) {
    return $this->_icon->getIconFrontImgBig($forumObj->getIconId());
  }
  
  public function getIconSmall($topicObj) {
    return $this->_icon->getIconFrontImgSmall($topicObj->getIconId());
  }

  public function getDisplayForums() {
    $this->_collection->setPageSize($this->getPageLimit());
    $this->_collection->setCurPage($this->getPageNum());
    return $this->_collection; 
  }
  
  public function getSortUrl($type = 'asc'){
    return $this->_helperUrl->getForumUrl(null, array(
        \WIO\Forum\Helper\Constant::WIO_FORUM_SORTING => $type,
        \WIO\Forum\Helper\Constant::WIO_FORUM_PAGE_NUM => 1
    ));
  }
  
  public function isAllowed($forumId) {
    $forumModel = $this->_forumModel->create()->load($forumId);
    if(!count($forumModel->getCustomerGroupId())) {
      return true;
    }
    if(!$this->getCustomer()->getId() 
            && !in_array(0, $forumModel->getCustomerGroupId())) {
      return false;
    }
    if(!in_array($this->getCustomer()->getCustomerGroupId(), $forumModel->getCustomerGroupId())) {
      return false;
    }
    return true;
  }
  
  public function getCustId() {
	  return $this->getCustomer()->getId();	  
  }
  
  protected function getUserId() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_USER_ID_PARAM);
  }
  
  public function getUserData() {
    $userId = $this->getUserId();
    $userData = $this->_forumUser->getForumUserData($userId);
    return $userData;
  }
  
  public function getUserDetails($_systemUserId) {
    if (!empty($this->_loadedUsers[$_systemUserId])) {
      return $this->_loadedUsers[$_systemUserId];
    }
    $this->_loadedUsers[$_systemUserId] = $this->_forumUser->getForumUserData($_systemUserId);
    return $this->_loadedUsers[$_systemUserId];
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
    if($this->getIconSmall($topic)) {
      return $this->getIconSmall($topic);
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
}
