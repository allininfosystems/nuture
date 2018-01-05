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

namespace WIO\Forum\Block\Topic\Edit;

class Form extends \WIO\Forum\Block\Topic\Edit {
  
  protected function _prepareLayout() {
    return parent::_prepareLayout();
  }

  public function getSubmitForumUrl() {
    return $this->getUrl(\WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/topic/save');
  }
  
  public function getIsIconsEnabled() {
    return $this->_forumData->getIsIconsEnabled();
  }
  
  public function getIsCustomerNotificationEnabled(){
    return $this->_forumData->getIsCustomerNotificationEnabled();
  }
  
  public function getForumId(){
    $forum = $this->getEditForum();
    if($forum) {
      return $this->getEditForum()->getId();
    }
  }
  
  public function getTopicId(){
    $topic = $this->getEditTopic();
    if($topic) {
      return $topic->getId();
    }
  }
  
  public function getTopicTitle() {
    $topic = $this->getEditTopic();
    if($topic) {
      return $topic->getTitle();
    }
  }
  
  public function getTopicDescription() {
    $topic = $this->getEditTopic();
    if($topic) {
      return $topic->getDescription();
    }
  }
  
  public function getPostId(){
    $post = $this->getEditPost();
    if($post){
      return $post->getId();
    }
  }
  
  public function getPostText(){
    $post = $this->getEditPost();
    if($post){
      return $post->getPost();
    }
  }
  
  public function getForumIdParamName() {
    return \WIO\Forum\Helper\Constant::WIO_FORUM_ID_PARAM_NAME;
  }
  
  public function getTopicIdParamName() {
    return \WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME;
  }
  
  public function getPostIdParamName(){
    return \WIO\Forum\Helper\Constant::WIO_FORUM_POST_ID_PARAM_NAME;
  }
}
