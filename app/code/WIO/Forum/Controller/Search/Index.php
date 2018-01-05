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

namespace WIO\Forum\Controller\Search;

class Index extends \Magento\Framework\App\Action\Action {

  protected $_resultPageFactory;
  protected $_forumUrl;
  protected $_search;
  protected $_forumSession;
  protected $_registry;
  protected $_params;
  
  protected $_type;
  
  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\View\Result\PageFactory $resultPageFactory,
    \WIO\Forum\Helper\Url $forumUrl,
    \WIO\Forum\Helper\Params $params,
    \Magento\Framework\Registry $registry, 
    \WIO\Forum\Model\Session $forumSession
  ) {
    $this->_forumUrl = $forumUrl;
    $this->_resultPageFactory = $resultPageFactory;
    $this->_forumSession = $forumSession;
    $this->_params = $params;
    $this->_registry = $registry;
    parent::__construct($context);
  }

  
  public function dispatch(
    \Magento\Framework\App\RequestInterface $request
  ){
    $this->_search = (
            $request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_PARAMNAME) 
            ? strip_tags($request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_PARAMNAME))
            : $this->_forumSession->getData(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_REGISTRATED));
    if(!$this->_search) {
      return $this->_redirect($this->_forumUrl->getForumUrl());
    }
    $this->_forumSession->setData(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_REGISTRATED, $this->_search);
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_REGISTRATED, $this->_search);
    $this->registerSearchType($request);
    $this->registerPageNumber($request);
    $this->registerPageLimit($request);
    $this->registerSortType($request);
    
    return parent::dispatch($request);
  }
  
  public function execute() {

    $resultPage = $this->_resultPageFactory->create();
    $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
    $breadcrumbs->addCrumb('forum_home', [
        'label' => __('Forum'),
        'title' => __('Forum'),
        'link' => '/' . $this->_forumUrl->getBaseForumUrl()
            ]
    );
    
    $breadcrumbs->addCrumb('forum_search', [
        'label' => __('Search Forum: "%1"', $this->_search),
        'title' => __('Search Forum: "%1"', $this->_search)
            ]
    );

    return $resultPage;
  }
  
  protected function getPageNumber($request) {
    $constant = ($this->_type == \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST
            ? \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST_PAGE 
            : \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_TOPIC_PAGE);
    return $this->_params->getPageNumber(
                    $request, $constant);
  }

  protected function getSortType($request) {
    $constant = ($this->_type == \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST
            ? \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST_SORT 
            : \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_TOPIC_SORT);
    
    return $this->_params->getSortType(
                    $request, $constant, \WIO\Forum\Helper\Constant::DEFAULT_SORT);
  }

  protected function getPageLimit($request) {
    $constant = ($this->_type == \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST
            ? \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST_LIMIT 
            : \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_TOPIC_LIMIT);
    
    return $this->_params->getPageLimit(
                    $request, $constant, \WIO\Forum\Helper\Constant::WIO_FORUM_DEFAULT_POST_PAGE_SIZE);
  }
  
  protected function registerPageNumber($request) {
    $constant = ($this->_type == \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST
            ? \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST_PAGE 
            : \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_TOPIC_PAGE);
   
    $pageNum = $this->getPageNumber($request);
    $this->_registry->register($constant, $pageNum);
  }

  protected function registerPageLimit($request) {
    $constant = ($this->_type == \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST
            ? \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST_LIMIT 
            : \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_TOPIC_LIMIT);
    
    $page_limit = $this->getPageLimit($request);
    $this->_registry->register($constant, $page_limit);
  }

  protected function registerSortType($request) {
    $constant = ($this->_type == \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST
            ? \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST_SORT 
            : \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_TOPIC_SORT);
    
    $sort_type = $this->getSortType($request);
    $this->_registry->register($constant, $sort_type);
  }

  protected function registerSearchType($request){
    $type = strip_tags($request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE));
    if (!$type) {
      $type = $this->_forumSession->getData(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE);
    }
    if($type != \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST &&
            $type != \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_TOPIC) {
      $type = \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST;
    }
    
    $this->_forumSession->setData(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE, $type);
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE, $type);
  
    $this->_type = $type;
  }
  
}
