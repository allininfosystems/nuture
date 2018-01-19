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

namespace WIO\Forum\Controller\Forum;

class Index extends \Magento\Framework\App\Action\Action {

  protected $_resultPageFactory;
  protected $_registry;
  protected $_params;
  protected $_customerSession;
  protected $_vistors;

  public function __construct(
    \Magento\Framework\App\Action\Context $context, 
    \Magento\Framework\View\Result\PageFactory $resultPageFactory, 
    \Magento\Framework\Registry $registry, 
    \WIO\Forum\Helper\Params $params,
    \Magento\Customer\Model\Session $customerSession,
    \WIO\Forum\Model\Visitors $visitors
  ) {
    parent::__construct($context);
    $this->_resultPageFactory = $resultPageFactory;
    $this->_registry = $registry;
    $this->_params = $params;
    $this->_customerSession = $customerSession;
    $this->_vistors = $visitors;
  }

  public function dispatch(
  \Magento\Framework\App\RequestInterface $request
  ) {
    $this->registerPageNumber($request);
    $this->registerPageLimit($request);
    $this->registerSortType($request);
    $this->registerCustomer();
    $this->_vistors->registerVisitor();
    return parent::dispatch($request);
  }

  public function execute() {
    $resultPage = $this->_resultPageFactory->create();
    $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
    if($breadcrumbs) {
      $breadcrumbs->addCrumb('forum_home', [
          'label' => __('Forum'),
          ' title' => __('Forum')
              ]
      );
    }
    return $resultPage;
  }
  
  protected function registerCustomer() {
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION, $this->_customerSession);
  }

  protected function registerPageNumber($request) {
    $pageNum = $this->getPageNumber($request);
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_PAGE_KEY_REGISTER, $pageNum);
  }

  protected function registerPageLimit($request) {
    /*$page_limit = $this->getPageLimit($request);*/
    $page_limit = 64;
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_LIMIT_KEY_REGISTER, $page_limit);
  }

  protected function registerSortType($request) {
    $sort_type = $this->getSortType($request);
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_SORT_KEY_REGISTER, $sort_type);
  }

  protected function getPageLimit($request) {
    return $this->_params->getPageLimit(
                    $request, \WIO\Forum\Helper\Constant::WIO_FORUM_LIMIT_KEY_REGISTER, \WIO\Forum\Helper\Constant::WIO_FORUM_DEFAULT_FORUM_PAGE_SIZE);
  }

  protected function getSortType($request) {
    return $this->_params->getSortType(
                    $request, \WIO\Forum\Helper\Constant::WIO_FORUM_SORT_KEY_REGISTER, \WIO\Forum\Helper\Constant::DEFAULT_SORT);
  }

  protected function getPageNumber($request) {
    return $this->_params->getPageNumber(
                    $request, \WIO\Forum\Helper\Constant::WIO_FORUM_PAGE_KEY_REGISTER);
  }

}
