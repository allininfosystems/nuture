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

namespace WIO\Forum\Model;

class User{
  
  protected $_usersLoaded = array();
  
  protected $_customerModel;
  protected $_forumUser;
  protected $_urlInterface;
  protected $_storeManager;
  protected $_postModel;
  protected $_topicModel;
  protected $_moderatorModel;
  
  protected $_joined;
  
  public function __construct(
  \Magento\Customer\Model\CustomerFactory $customerModel,
  UsersettingsFactory $forumUser,
  \Magento\Framework\UrlInterface $urlInterface,
  \Magento\Store\Model\StoreManagerInterface $storeManager,
  \WIO\Forum\Model\Moderator $moderatorModel,
  PostFactory $postModel,
  TopicFactory $topicModel
  ) {
    $this->_forumUser     = $forumUser;
    $this->_customerModel = $customerModel->create();
    $this->_urlInterface  = $urlInterface;
    $this->_storeManager  = $storeManager;
    $this->_postModel = $postModel;
    $this->_topicModel = $topicModel;
    $this->_moderatorModel = $moderatorModel;
  }
  
  
  public function getIsModerator($_userId) {
    return $this->_moderatorModel->isModerator($_userId);
  }
  
  public function getJoined(){
    return $this->_joined;
  }
  
  public function getTotalUserTopics($_userId) {
    $topicCollection = $this->_topicModel->create()
            ->getCollection();
    $topicCollection->getUserTopics($_userId);
    
    return $topicCollection->getSize();
  }
  
  public function getTotalUserPosts($_userId) {
    $postCollection = $this->_postModel->create()->getCollection();
    $postCollection->getUserPosts($_userId);
    $postCollection->setOrder(\WIO\Forum\Helper\Constant::WIO_FORUM_CREATED_TIME_SORT_FIELD, 'asc');
    $firstItem = $postCollection->getFirstItem();
    if($firstItem->getId()){
      $this->_joined = $firstItem->getCreatedTime();
    }else{
      $this->_joined = null;
    }
    return $postCollection->count();
  }
  
  public function getForumUserName($_userId, $modelForumUser = null) {
    if( $modelForumUser === null) {
      $modelForumUser = $this->_forumUser->create();
      $modelForumUser->load($_userId, 'system_user_id');
    }
    if($modelForumUser->getNickname() && $modelForumUser->getId()) {
      return $modelForumUser->getNickname();
    }
    elseif($_userId == \WIO\Forum\Helper\Constant::WIO_FORUM_ADMIN_ID) {
      return \WIO\Forum\Helper\Constant::WIO_FORUM_ADMIN_USERNAME;
    }else{
      return $this->getMagentoCustomerFirstAndLastName($_userId);
      //return 'Should be an userName';
    }
  }
  
  public function getForumUserSignature($_userId, $modelForumUser = null) {
    if( $modelForumUser === null) {
      $modelForumUser = $this->_forumUser->create();
      $modelForumUser->load($_userId, 'system_user_id');
    }
    if($modelForumUser->getSignature() && $modelForumUser->getId()) {
      return $modelForumUser->getSignature();
    }
  }
  
  public function getForumUserAvatar($_userId, $modelForumUser = null){
    if( $modelForumUser === null) {
      $modelForumUser = $this->_forumUser->create();
      $modelForumUser->load($_userId, 'system_user_id');
    }
    if($modelForumUser->getAvatar() && $modelForumUser->getId()) {
      return $this->getAvatarImage($modelForumUser->getAvatar());
    }else{
      return $this->getNoAvatarImage();
    }
  }
  
  public function getForumUserData($_userId){
    
    if($_userId == \WIO\Forum\Helper\Constant::WIO_FORUM_ADMIN_USERNAME){
      $_userId = \WIO\Forum\Helper\Constant::WIO_FORUM_ADMIN_ID;
    }
    
    if(!empty($this->_usersLoaded[$_userId])) {
      return $this->_usersLoaded[$_userId];
    }
    
    $modelForumUser = $this->_forumUser->create();
    $modelForumUser->load($_userId, 'system_user_id');
    
    $this->_usersLoaded[$_userId] = array(
        'avatar'      => $this->getForumUserAvatar($_userId, $modelForumUser), 
        'avatar_real' => $modelForumUser->getAvatar(),
        'nickname'    => $this->getForumUserName($_userId, $modelForumUser),
        'signature'   => $this->getForumUserSignature($_userId, $modelForumUser),
        'userId' => $_userId,
        'total_posts' => $this->getTotalUserPosts($_userId),
        'total_topics' => $this->getTotalUserTopics($_userId),
        'link' => $this->getUserLink($_userId),
        'joined' => $this->getJoined(),
        'empty' => (!$modelForumUser->getId() && !$this->_customerModel->getId() ? true : false),
        'role' => $this->getUserRole($_userId)
    );
    
    return $this->_usersLoaded[$_userId];
  }
  
  public function getUserLink($_userId) {
    if($_userId == \WIO\Forum\Helper\Constant::WIO_FORUM_ADMIN_ID) {
      $_userId = \WIO\Forum\Helper\Constant::WIO_FORUM_ADMIN_USERNAME;
    }
    return $this->_urlInterface->getUrl(\WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/customer/view/', array(\WIO\Forum\Helper\Constant::WIO_FORUM_USER_ID_PARAM => $_userId));
  }
  
  protected function getUserRole($_userId) {
    if($_userId == \WIO\Forum\Helper\Constant::WIO_FORUM_ADMIN_ID) {
      return __('Administrator');
    }elseif($this->getIsModerator($_userId)) {
      return __('Moderator');
    }else{
      return __('User');
    }
  }
  
  protected function getMagentoCustomerFirstAndLastName($_userId) {
    $this->_customerModel->load($_userId);
    if($this->_customerModel->getFirstname() || $this->_customerModel->getLastname()) {
      return $this->_customerModel->getFirstname() . ' '  .$this->_customerModel->getLastname();
    }
    
    return __('Not Set');
  }
  
  protected function getAvatarImage($srcImg) {
    return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 
            $srcImg;
  }
  
  protected function getNoAvatarImage() {
    return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 
    \WIO\Forum\Helper\Constant::WIO_FORUM_AVATR_FILE_PATH . '/' . \WIO\Forum\Helper\Constant::WIO_FORUM_AVATAR_NO_IMAGE;
  }
}