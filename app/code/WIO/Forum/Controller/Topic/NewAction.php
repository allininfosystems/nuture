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

class NewAction extends \Magento\Framework\App\Action\Action {

  protected $_resultPageFactory;
  protected $_customerSession;
  
  public function __construct(
    \Magento\Framework\App\Action\Context $context, 
    \Magento\Framework\View\Result\PageFactory $resultPageFactory, 
    \Magento\Customer\Model\Session $customerSession    
  ) {
    parent::__construct($context);
    $this->_resultPageFactory = $resultPageFactory;
    $this->_customerSession   = $customerSession;
  }
  
  public function dispatch(
    \Magento\Framework\App\RequestInterface $request
  ){
      if (!$this->_customerSession->isLoggedIn()) {
          $this->_customerSession->setAfterAuthUrl($this->_url->getCurrentUrl());
          $this->_customerSession->authenticate();
      }
      return parent::dispatch($request);
  }
  
  public function execute() {
    return $this->_forward('edit');
  }
  
}
