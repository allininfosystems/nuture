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

namespace WIO\Forum\Controller\Customer;

class Posts extends \Magento\Framework\App\Action\Action {
  
  protected $_resultPageFactory;
  protected $_registry;
  protected $_customerSession;
  protected $_params;
  
  public function __construct(
    \Magento\Framework\App\Action\Context $context, 
    \Magento\Customer\Model\Session $customerSession,
    \Magento\Framework\View\Result\PageFactory $resultPageFactory,  
    \WIO\Forum\Helper\Params $params,
    \Magento\Framework\Registry $registry       
  ) {
    $this->_customerSession = $customerSession;
    $this->_resultPageFactory = $resultPageFactory;
    $this->_registry = $registry;
    $this->_params = $params;
    parent::__construct($context);
  }
  
  public function dispatch(
    \Magento\Framework\App\RequestInterface $request
  ){
    if (!$this->_customerSession->isLoggedIn()) {
        $this->_customerSession->setAfterAuthUrl($this->_url->getCurrentUrl());
        $this->_customerSession->authenticate();
        return parent::dispatch($request);
        
    }else{
      $this->registerPageNumber($request);
      $this->registerPageLimit($request);
      $this->registerSortType($request);
      $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION, $this->_customerSession);
    }
    return parent::dispatch($request);
  }
 
  
  public function execute() {
    $resultPage = $this->_resultPageFactory->create();
    return $resultPage;
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
