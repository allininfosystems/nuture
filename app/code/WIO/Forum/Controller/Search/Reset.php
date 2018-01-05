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

class Reset extends \Magento\Framework\App\Action\Action {
  
  protected $_forumSession;
  
  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \WIO\Forum\Model\Session $forumSession
  ) {
    $this->_forumSession = $forumSession;
    parent::__construct($context);
  }
  
  public function execute() {
    $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
    $this->_forumSession->setData(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_REGISTRATED, null);
    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
  
    return $resultRedirect;
  }
}
