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

namespace WIO\Forum\Block\Topic\Index;

class ListPosts extends \WIO\Forum\Block\Topic\Index {

  protected $_collection;
  protected $_loadedUsers;

  protected function _prepareLayout() {
    $this->_collection = $this->getAllPosts();
    parent::_prepareLayout();
    $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager', 'wioforum.forum.pager'
    );
    $pager->setLimit($this->getPageLimit())
            ->setShowAmounts(true)
            ->setCollection($this->_collection);
    $pager->setAvailableLimit(array(/* 2=>2, */ 10 => 10, 20 => 20, 30 => 30, 50 => 50));
    $this->setChild('pager', $pager);

    return $this;
  }

  public function getPagerHtml() {
    return $this->getChildHtml('pager');
  }

  public function getIsIconsEnabled() {
    return $this->_forumData->getIsIconsEnabled();
  }

  public function getIconSrc($model = NULL) {
    $_model = $model ? $model : $this->getParentTopic();
    return $this->_icon->getIconFrontImgSmall($_model->getIconId());
  }

  public function getDisplayPosts() {
    $this->_collection->setPageSize($this->getPageLimit());
    $this->_collection->setCurPage($this->getPageNum());
    return $this->_collection;
  }

  public function getSortUrl($topic = null, $type = 'asc') {
    $forum = $this->getParentForum();
    if ($topic == null) {
      $topic = $this->getParentTopic();
    }
    return $this->_helperUrl->getTopicUrl($forum, $topic, array(
                \WIO\Forum\Helper\Constant::WIO_FORUM_SORTING => $type,
                \WIO\Forum\Helper\Constant::WIO_FORUM_PAGE_NUM => 1
    ));
  }

  public function getUserDetails($_systemUserId) {
    if (!empty($this->_loadedUsers[$_systemUserId])) {
      return $this->_loadedUsers[$_systemUserId];
    }
    $this->_loadedUsers[$_systemUserId] = $this->_forumUser->getForumUserData($_systemUserId);
    return $this->_loadedUsers[$_systemUserId];
  }

  public function getTopicTitle() {
    $topicModel = $this->getParentTopic();
    return $topicModel->getTitle();
  }

  public function getPostTime($posModel) {
    return $this->_forumData->getTimeAccordingToTimeZone($posModel->getCreatedTime());
  }

  public function getAddNewUrl() {
    $parentForum = $this->getParentForum();
    return $this->_helperUrl->getAddTopicUrl($parentForum);
  }

  public function getAddPostNewUrl() {
    $parentForum = $this->getParentForum();
    $parentTopic = $this->getParentTopic();
    return $this->_helperUrl->getAddTopicUrl($parentForum, $parentTopic);
  }

  public function getEditPostUrl($post) {
    $parentForum = $this->getParentForum();
    $parentTopic = $this->getParentTopic();
    return $this->_helperUrl->getAddTopicUrl($parentForum, $parentTopic, $post);
  }
  
  public function getDeletePostUrl($post){
    return $this->_helperUrl->getDeletePostUrl($post->getId());
  }

  public function getActionPost() {
    return $this->getUrl(\WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/topic/save');
  }

  public function getForumIdParamName() {
    return \WIO\Forum\Helper\Constant::WIO_FORUM_ID_PARAM_NAME;
  }

  public function getTopicIdParamName() {
    return \WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME;
  }

  public function getPostBlockId($postModel) {
    return \WIO\Forum\Helper\Constant::WIO_FORUM_POST_BLOCK_ID . $postModel->getId();
  }

  public function getIsCustomerNotificationEnabled(){
    return $this->_forumData->getIsCustomerNotificationEnabled();
  }
  
}
