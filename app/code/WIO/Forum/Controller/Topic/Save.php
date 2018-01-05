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

namespace WIO\Forum\Controller\Topic;

use WIO\Forum\Model\Url;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Save extends \Magento\Framework\App\Action\Action {

  protected $_customerSession;
  protected $_postModel;
  protected $_topicModel;
  protected $_forumModel;
  protected $_notify;
  protected $_postDataProcessor;
  protected $_modelUrl;
  protected $_forumData;
  protected $_postData;
  protected $_helperUrl;
  protected $_date;
  protected $_moderatorModel;
  protected $flag_topic_is_new = false;
  protected $flag_post_is_new = false;

  public function __construct(
    \Magento\Framework\App\Action\Context $context, 
    \Magento\Customer\Model\Session $customerSession,
    \WIO\Forum\Model\PostFactory $postModel, 
    \WIO\Forum\Model\Notify $notify,
    \WIO\Forum\Model\TopicFactory $topicModel, 
    \WIO\Forum\Model\ForumFactory $forumModel, 
    \WIO\Forum\Helper\Data $forumData, 
    PostDataProcessor $postDataProcessor, 
    Url $forumUrl, 
    DateTime $date,
    \WIO\Forum\Model\Moderator $moderatorModel,
    \WIO\Forum\Helper\Url $helperUrl
  ) {
    parent::__construct($context);
    $this->_customerSession = $customerSession;
    $this->_postModel = $postModel->create();
    $this->_topicModel = $topicModel->create();
    $this->_forumModel = $forumModel->create();
    $this->_postDataProcessor = $postDataProcessor;
    $this->_modelUrl = $forumUrl;
    $this->_date = $date;
    $this->_forumData = $forumData;
    $this->_helperUrl = $helperUrl;
    $this->_notify = $notify;
    $this->_moderatorModel = $moderatorModel;
  }

  public function dispatch(
  \Magento\Framework\App\RequestInterface $request
  ) {
    if (!$this->_customerSession->isLoggedIn()) {
      $this->_customerSession->setAfterAuthUrl($this->_url->getCurrentUrl());
      $this->_customerSession->authenticate();
      return parent::dispatch($request);
    }
    if (!$this->validate($request)) {
      return $this->_redirect('');
    }
    $post = $request->getPostValue();
    $this->_postData = $this->_postDataProcessor->filter($post);
    return parent::dispatch($request);
  }

  public function execute() {
    $flag_topic_saved = false;
    try {
      //save topic first
      if (!$this->_topicModel || !$this->_topicModel->getId() || 
              $this->getIsOwner($this->_topicModel)) {
        $topicId = $this->saveTopic();
        $flag_topic_saved = true;
      }else{
        $topicId = $this->_topicModel->getId();
      }
      if (!$topicId) {
        $this->_redirect('');
        return;
      }
      //save post
      $this->savePost($topicId);
      if ($flag_topic_saved) {
        $this->messageManager->addSuccess($this->getTopicSavedMessage());
      }
      $this->messageManager->addSuccess($this->getPostSavedMessage());
    } catch (\Exception $e) {
      $this->messageManager->addError($e->getMessage());
    }
    $this->_redirect($this->getRedirectUrl());
  }
  
  
  protected function getIsModerator() {
    if($this->_customerSession->getId()) {
      return $this->_moderatorModel->isModerator($this->_customerSession->getId());
    }
  }
  
  public function getIsOwner($object) {
    if($this->getIsModerator()) {
      return true;
    }
    if($object->getSystemUserId() == $this->_customerSession->getId()) {
      return true;
    }
  }

  protected function getRedirectUrl() {
    return $this->_helperUrl->getLatestViewUrl($this->_postModel->getId());
  }

  protected function getPostSavedMessage() {
    if ($this->flag_post_is_new) {
      if ($this->getStatus() == 0) {
        return __('Post successfully saved and will be added after moderation!');
      }
      return __('Post successfully saved!');
    }
    if ($this->getStatus() == 0) {
      return __('Post successfully updated and will be added after moderation!');
    }
    return __('Post successfully updated!');
  }

  protected function getTopicSavedMessage() {
    if ($this->flag_topic_is_new) {
      if ($this->getStatus() == 0) {
        return __('Topic successfully saved and will be added after moderation!');
      }
      return __('Topic successfully saved!');
    }
    if ($this->getStatus() == 0) {
      return __('Topic successfully updated and will be added after moderation!');
    }
    return __('Topic successfully updated!');
  }

  protected function saveTopic() {
    if($this->getRequest()->getParam('post_only')){
      return $this->_topicModel->getId(); 
    }
    if (!$this->_topicModel->getId()) {
      $this->flag_topic_is_new = true;
      $url_text = $this->_modelUrl->buildUrlKeyFromTitle($this->_postData['title'], false);
      $this->_topicModel->setUrlText($url_text);
      $this->_topicModel->setCreatedTime($this->_date->gmtDate());
    } else {
      $this->_topicModel->setUpdatedTime($this->_date->gmtDate());
    }
    if(!empty($this->_postData['icon_id'])
            && $this->_postData['icon_id'][0] !== '' ) {
      $this->_topicModel->setIconId($this->_postData['icon_id'][0]);
    }else{
      $this->_topicModel->setIconId('');
    }
    $this->_topicModel->setTitle($this->_postData['title']);
    $this->_topicModel->setDescription($this->_postData['description']);
    //status moderation
    $this->_topicModel->setStatus($this->getStatus());
    $this->_topicModel->setParentId($this->_forumModel->getId());
    if(!$this->_topicModel->getSystemUserId()) {
      $this->_topicModel->setSystemUserId($this->_customerSession->getId());
    }
    $this->_topicModel->save();
    return $this->_topicModel->getId();
  }

  protected function savePost($topicId) {
    $post = $this->_postData['post'];
    $postOrig = strip_tags($post);
    if (!$this->_postModel->getId()) {
      $this->flag_post_is_new = true;
      $this->_postModel->setCreatedTime($this->_date->gmtDate());
    } else {
      $this->_postModel->setUpdatedTime($this->_date->gmtDate());
    }
    if(!$this->_postModel->getSystemUserId()) {
      $this->_postModel->setSystemUserId($this->_customerSession->getId());
    }
    //status moderation
    $this->_postModel->setStatus($this->getStatus());
    $this->_postModel->setParentId($topicId);
    $this->_postModel->setPost($post);
    $this->_postModel->setPostOrig($postOrig);
    $this->_postModel->save();
    if($this->_forumData->getIsCustomerNotificationEnabled()) {
      $this->saveNotification($topicId, $this->_customerSession->getId());
      $this->sendNotifications($this->_topicModel, $this->_customerSession->getId(), $this->_postModel);
    }
  }
  
  protected function sendNotifications($topic, $customerId, $post_id){
    $this->_notify->sendNotifications($topic, $customerId, $post_id);
  } 

  protected function saveNotification($topicId){
    if(empty($this->_postData['notify_my'])) {
      return;
    }
    $this->_notify->saveNew($topicId, $this->_customerSession->getId());
  }
  
  protected function validate($request) {
    if (!$this->validateForum($request)) {
      return false;
    }
    if (!$this->validateTopic($request)) {
      return false;
    }
    if (!$this->validatePost($request)) {
      return false;
    }
    return true;
  }

  protected function validateTopic($request) {
    $topic_id = $request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME);
    if(!$topic_id) {
      return true;
    }
    $this->_topicModel->load($topic_id);
    if (!$this->_topicModel->getId() 
            || $this->_topicModel->getIsDeleted() 
            || $this->_topicModel->getStatus() == 0) {
      return false;
    }
    
    return true;
  }

  protected function validatePost($request) {
    $post_id = $request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_ID_PARAM_NAME);
    if(!$post_id){
      return true;
    }
    $this->_postModel->load($post_id);
    if(!$this->_postModel->getId() 
            || $this->_postModel->getIsDeleted() 
            || $this->_postModel->getStatus() == 0
            || !$this->getIsOwner($this->_postModel)){
      return false;
    }
    return true;
  }

  protected function getStatus() {
    return 1;
  }

  protected function validateForum($request) {
    $forum_id = $request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_ID_PARAM_NAME);
    if (!$forum_id) {
      return false;
    }
    $this->_forumModel->load($forum_id);
    if (!$this->_forumModel->getId() || $this->_forumModel->getIsDeleted() || $this->_forumModel->getStatus() == 0) {
      return false;
    }
    return true;
  }

}
