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

namespace WIO\Forum\Controller\Notify;

class Remove extends \Magento\Framework\App\Action\Action {

  protected $_notify;
  protected $_helperUrl;
  
  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \WIO\Forum\Model\Notify $notify,
    \WIO\Forum\Helper\Url $helperUrl
  ) {
    $this->_helperUrl = $helperUrl;
    $this->_notify = $notify;
    parent::__construct($context);
  }
  
  public function execute() {
    $hash = $this->getRequest()->getParam('hash');
    $removed = $this->_notify->remove($hash);
    if($removed) {
      $this->messageManager->addSuccess(__('You are unsubscribed from Topic'));
    }
    $this->_redirect($this->_helperUrl->getForumUrl());
  }
}