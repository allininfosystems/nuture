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

namespace WIO\Forum\Controller\Adminhtml\Forum;

class Index extends \WIO\Forum\Controller\Adminhtml\Index {

  public function execute() {

    $resultPage = $this->resultPageFactory->create();

    $resultPage->setActiveMenu('WIO_Forum::forum');
    $resultPage->getConfig()->getTitle()->prepend(__('Manage Forums'));

    $resultPage->addBreadcrumb(__('Forum'), __('Forum'));
    $resultPage->addBreadcrumb(__('Forum Manager'), __('Forum Manager'));


    return $resultPage;
  }

}
