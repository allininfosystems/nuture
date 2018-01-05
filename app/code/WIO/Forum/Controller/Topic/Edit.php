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

class Edit extends \Magento\Framework\App\Action\Action {

  protected $_resultPageFactory;
  protected $_customerSession;
  protected $_forumModel;
  protected $_topicModel;
  protected $_postModel;
  protected $_registry;
  protected $_forumUrl;
  protected $_storeManager;
  protected $_moderatorModel;
  
  public function __construct(
    \Magento\Framework\App\Action\Context $context, 
    \Magento\Framework\View\Result\PageFactory $resultPageFactory, 
    \Magento\Framework\Registry $registry,       
    \Magento\Customer\Model\Session $customerSession,
    \WIO\Forum\Model\ForumFactory $forumModel,
    \WIO\Forum\Model\TopicFactory $topicModel,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \WIO\Forum\Model\PostFactory $postModel,
    \WIO\Forum\Model\Moderator $moderatorModel,
    \WIO\Forum\Helper\Url $forumUrl
  ) {
    parent::__construct($context);
    $this->_resultPageFactory = $resultPageFactory;
    $this->_customerSession   = $customerSession;
    $this->_forumModel = $forumModel->create();
    $this->_registry = $registry;
    $this->_forumUrl = $forumUrl;
    $this->_topicModel = $topicModel->create();
    $this->_postModel = $postModel->create();
    $this->_storeManager = $storeManager;
    $this->_moderatorModel = $moderatorModel;
  }
  
  public function dispatch(
    \Magento\Framework\App\RequestInterface $request
  ){
      if (!$this->_customerSession->isLoggedIn()) {
          $this->_customerSession->setAfterAuthUrl($this->_url->getCurrentUrl());
          $this->_customerSession->authenticate();
      }else{
        
        $this->registerEditObjects($request);
        $forumObj = $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_FORUM_OBJECT);
        if(!$forumObj->getId()){
          return $this->_redirect('/');
        }
        if($request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME)
                && !$this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_TOPIC_OBJECT)) {
          
          return $this->_redirect('/');
        }
        if($request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_ID_PARAM_NAME)
                && !$this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_POST_OBJECT)){
          
          return $this->_redirect('/');
        }
        /*** add authorization here ***/
        if(!$this->isAllowed()) {
          return $this->_redirect($this->_forumUrl->getForumUrl());
        }
        /******************************/

      }
      return parent::dispatch($request);
  }
  
  public function execute() {
    $resultPage = $this->_resultPageFactory->create();
    $forumObj = $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_FORUM_OBJECT);
    $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
    
    $breadcrumbs->addCrumb('forum_home', [
        'label' => __('Forum'),
        'title' => __('Forum'),
        'link' =>  '/' . $this->_forumUrl->getBaseForumUrl()
            ]
    );
    
    if ($forumObj) {
      $breadcrumbs->addCrumb('forum_view', [
          'label' => $forumObj->getTitle(),
          'title' => $forumObj->getTitle(),
          'link' => $this->_forumUrl->getForumUrl($forumObj)
              ]
      );
    }
    $this->addCrumbs($breadcrumbs, $forumObj);
    
    return $resultPage;
  }
  
  protected function getIsModerator() {
    if($this->_customerSession->getId()) {
      return $this->_moderatorModel->isModerator($this->_customerSession->getId());
    }
  }
  
  protected function isAllowed() {
    if($this->getIsModerator()) {
      return true;
    }
    if($this->_forumModel->getStoreId()) {
      if($this->_storeManager->getStore()->getId() !== $this->_forumModel->getStoreId()) {
        return false;
      }
    }
    if((count($this->_forumModel->getCustomerGroupId()) 
            && empty($this->_customerSession->getCustomerGroupId()))
            && !in_array(0, $this->_forumModel->getCustomerGroupId())
            || (count($this->_forumModel->getCustomerGroupId()) && !in_array($this->_customerSession->getCustomerGroupId(), $this->_forumModel->getCustomerGroupId()))) {
      $this->messageManager->addError(__('You have no access to view this forum'));
      return false;
    } 
    return true;
  }
  
  protected function addCrumbs($breadcrumbs, $forumObj){
    $topicObj = $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_TOPIC_OBJECT);
    if($topicObj && $topicObj->getId()) {
      $breadcrumbs->addCrumb('forum_topic', [
        'label' => __('Topic: %1', $topicObj->getTitle()),
        'title' => __('Topic: %1', $topicObj->getTitle()),
        'link' => $this->_forumUrl->getTopicUrl($forumObj, $topicObj)
              ]
      );
      if($this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_POST_OBJECT)){
        $breadcrumbs->addCrumb('forum_post', [
          'label' => __('Edit Post'),
          'title' => __('Edit Post'),
                ]
        );
      }else{
        $breadcrumbs->addCrumb('forum_new_post', [
          'label' => __('Add New Post'),
          'title' => __('Add New Post'),
                ]
        );
      }
    }else{
      $breadcrumbs->addCrumb('forum_topic', [
        'label' => __('Add New Topic'),
        'title' => __('Add New Topic')
              ]
      );  
    }
  }
  
  protected function registerEditObjects( 
    \Magento\Framework\App\RequestInterface $request
  ){
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION, $this->_customerSession);
    $forum_id = $request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_ID_PARAM_NAME);
    $this->_forumModel->load($forum_id);
    if(!$this->_forumModel->getIsDeleted() && $this->_forumModel->getStatus()) {
      $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_FORUM_OBJECT, $this->_forumModel);
    }
    $topic_id = $request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME);
    $this->_topicModel->load($topic_id);
    if(($this->_topicModel->getId() 
            && $this->_topicModel->getStatus()
            && !$this->_topicModel->getIsDeleted()
            && $this->_topicModel->getParentId() == $this->_forumModel->getId())
            || $this->getIsModerator()) {
      
      $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_TOPIC_OBJECT, $this->_topicModel);
    }else{
      $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_TOPIC_OBJECT, null);
    }
    $post_id = $request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_ID_PARAM_NAME);
    $this->_postModel->load($post_id);
    
    if(($this->_postModel->getId() 
            && $this->_postModel->getStatus()
            && !$this->_postModel->getIsDeleted()
            && $this->_postModel->getForumId() == $this->_forumModel->getId()
            && $this->_customerSession->getId() == $this->_postModel->getSystemUserId())
            || $this->getIsModerator()) {
      
      $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_POST_OBJECT, $this->_postModel);
    }else{
      $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_EDIT_POST_OBJECT, null);
    }
    
  }
  
  protected function isOwner() {
    if($this->_customerSession->getId() == $this->_topicModel->getSystemUserId()) {
      return true;
    }
  }
}
