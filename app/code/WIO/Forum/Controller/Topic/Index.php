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

class Index extends \Magento\Framework\App\Action\Action {

  protected $_resultPageFactory;
  protected $_registry;
  protected $_params;
  protected $_topicModel;
  protected $_forumModel;
  protected $_forumUrl;
  protected $_customerSession;
  protected $_vistors;
  protected $_topicViews;
  protected $_storeManager;
  protected $_moderatorModel;
  
  
  public function __construct(
    \Magento\Framework\App\Action\Context $context, 
    \Magento\Framework\View\Result\PageFactory $resultPageFactory, 
    \Magento\Framework\Registry $registry, 
    \WIO\Forum\Helper\Params $params,
    \WIO\Forum\Model\TopicFactory $topicModel,
    \Magento\Customer\Model\Session $customerSession,
    \WIO\Forum\Model\ForumFactory $forumModel,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \WIO\Forum\Helper\Url $forumUrl,
    \WIO\Forum\Model\TopicView $topicViews,
    \WIO\Forum\Model\Moderator $moderatorModel,
    \WIO\Forum\Model\Visitors $visitors      
  ) {
    parent::__construct($context);
    $this->_topicViews = $topicViews;
    $this->_resultPageFactory = $resultPageFactory;
    $this->_registry = $registry;
    $this->_params = $params;
    $this->_topicModel = $topicModel->create();
    $this->_forumModel = $forumModel->create();
    $this->_forumUrl = $forumUrl;
    $this->_customerSession = $customerSession;
    $this->_vistors = $visitors;
    $this->_topicViews = $topicViews;
    $this->_storeManager = $storeManager;
    $this->_moderatorModel = $moderatorModel;
  }

  public function dispatch(
    \Magento\Framework\App\RequestInterface $request
  ) {
    $topicId = intval($request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME));
    if(!$topicId){
      return $this->_redirect('');
    }
    $topicModel = $this->_topicModel->load($topicId);
    if(!$topicModel->getId() || $topicModel->getIsDeleted()) {
      return $this->_redirect('');
    }
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_TOPIC, $topicModel);
    $forumId = $topicModel->getParentId();
    
    $forumModel = $this->_forumModel->load($forumId);
    if(!$forumModel->getId() || $forumModel->getIsDeleted()) {
      return $this->_redirect('');
    }
    //add authorization here//
    if(!$this->isAllowed()) {
      return $this->_redirect($this->_forumUrl->getForumUrl());
    }
    /************************/
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_FORUM, $forumModel);
    
    $this->registerPageNumber($request);
    $this->registerPageLimit($request);
    $this->registerSortType($request);
    $this->registerCustomer();
    $this->_vistors->registerVisitor();
    return parent::dispatch($request);
  }

  public function execute() {
    $this->_topicViews->updateViews();
    $resultPage = $this->_resultPageFactory->create();
    $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
    if ($breadcrumbs) {
      $breadcrumbs->addCrumb('forum_home', [
          'label' => __('Forum'),
          'title' => __('Forum'),
          'link' =>  '/' . $this->_forumUrl->getBaseForumUrl()
              ]
      );
      
      $breadcrumbs->addCrumb('forum_view', [
        'label' => $this->_registry
            ->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_FORUM)
            ->getTitle(),
        'title' => $this->_registry
            ->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_FORUM)
            ->getTitle(),
        'link' => $this->_forumUrl->getForumUrl($this->_registry
            ->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_FORUM))
            ]
      );
      
      
      
      $breadcrumbs->addCrumb('topic_view', [
        'label' => $this->_registry
            ->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_TOPIC)
            ->getTitle(),
        'title' => $this->_registry
            ->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_TOPIC)
            ->getTitle()
            ]
      );
    }
    return $resultPage;
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
  
  
  protected function getIsModerator() {
    if($this->_customerSession->getId()) {
      return $this->_moderatorModel->isModerator($this->_customerSession->getId());
    }
  }
  
  protected function registerCustomer() {
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION, $this->_customerSession);
  }
  
  protected function registerPageNumber($request) {
    $pageNum = $this->getPageNumber($request);
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_PAGE_KEY_REGISTER, $pageNum);
  }

  protected function registerPageLimit($request) {
    $page_limit = $this->getPageLimit($request);
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_LIMIT_KEY_REGISTER, $page_limit);
  }

  protected function registerSortType($request) {
    $sort_type = $this->getSortType($request);
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_SORT_KEY_REGISTER, $sort_type);
  }

  protected function getPageLimit($request) {
    return $this->_params->getPageLimit(
                    $request, \WIO\Forum\Helper\Constant::WIO_FORUM_POST_LIMIT_KEY_REGISTER, \WIO\Forum\Helper\Constant::WIO_FORUM_DEFAULT_POST_PAGE_SIZE);
  }

  protected function getSortType($request) {
    return $this->_params->getSortType(
                    $request, \WIO\Forum\Helper\Constant::WIO_FORUM_POST_SORT_KEY_REGISTER, \WIO\Forum\Helper\Constant::DEFAULT_SORT);
  }

  protected function getPageNumber($request) {
    return $this->_params->getPageNumber(
                    $request, \WIO\Forum\Helper\Constant::WIO_FORUM_POST_PAGE_KEY_REGISTER);
  }

}
