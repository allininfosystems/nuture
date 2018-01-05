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

namespace WIO\Forum\Controller\Adminhtml;

use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Registry;
use Magento\Backend\Model\View\Result\ForwardFactory;

abstract class Index extends \Magento\Backend\App\Action {

  protected $_coreRegistry;
  protected $resultPageFactory;
  protected $resultForwardFactory;

  public function __construct(
    \Magento\Backend\App\Action\Context $context, 
    Registry $registry, 
    PageFactory $resultPageFactory, 
    ForwardFactory $resultForwardFactory
  ) {
    $this->_coreRegistry = $registry;
    $this->resultPageFactory = $resultPageFactory;
    $this->resultForwardFactory = $resultForwardFactory;

    parent::__construct($context);
  }

  protected function initPageForum($resultPage) {
    $resultPage->setActiveMenu('WIO_Forum::forum_manager')
            ->addBreadcrumb(__('Forum'), __('Forum'));
    return $resultPage;
  }

}
