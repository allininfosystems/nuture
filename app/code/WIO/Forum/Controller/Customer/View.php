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

class View extends \Magento\Framework\App\Action\Action {

  protected $_customerSession;
  protected $_resultPageFactory;
  protected $_registry;
  protected $_forumUrl;

  public function __construct(
    \Magento\Framework\App\Action\Context $context, 
    \Magento\Customer\Model\Session $customerSession, 
    \Magento\Framework\View\Result\PageFactory $resultPageFactory, 
    \WIO\Forum\Helper\Url $forumUrl,
    \Magento\Framework\Registry $registry
  ) {
    parent::__construct($context);
    $this->_customerSession = $customerSession;
    //$this->_url = $urlInterface;
    $this->_resultPageFactory = $resultPageFactory;
    $this->_registry = $registry;
    $this->_forumUrl = $forumUrl;
  }

  public function dispatch(
  \Magento\Framework\App\RequestInterface $request
  ) {
    if (!$this->_customerSession->isLoggedIn()) {
      $this->_customerSession->setAfterAuthUrl($this->_url->getCurrentUrl());
      $this->_customerSession->authenticate();
      return parent::dispatch($request);
    }else{
      
    }
    return parent::dispatch($request);
  }

  public function execute() {
    $userId = $this->getRequest()->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_USER_ID_PARAM);
    if(!$userId) {
      $this->_redirect('/');
    }
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_USER_ID_PARAM, $userId);
    $resultPage = $this->_resultPageFactory->create();
    $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
    $breadcrumbs->addCrumb('forum_home', [
        'label' => __('Forum'),
        'title' => __('Forum'),
        'link' =>  '/' . $this->_forumUrl->getBaseForumUrl()
            ]
    );
    $breadcrumbs->addCrumb('forum_user_view', [
        'label' => __('View Forum User'),
        'title' => __('View Forum User')
      ]
    );
    return $resultPage;
  }

}
