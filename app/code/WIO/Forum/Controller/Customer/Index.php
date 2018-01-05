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

class Index extends \Magento\Framework\App\Action\Action {

  protected $_resultPageFactory;
  protected $_registry;
  protected $_customerSession;
  
  public function __construct(
    \Magento\Framework\App\Action\Context $context, 
    \Magento\Customer\Model\Session $customerSession,
    \Magento\Framework\View\Result\PageFactory $resultPageFactory, 
    \Magento\Framework\Registry $registry       
  ) {
    $this->_customerSession = $customerSession;
    $this->_resultPageFactory = $resultPageFactory;
    $this->_registry = $registry;
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
      $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION, $this->_customerSession);
    }
    return parent::dispatch($request);
  }
  
  public function execute() {
    $resultPage = $this->_resultPageFactory->create();
    return $resultPage;
  }

}
