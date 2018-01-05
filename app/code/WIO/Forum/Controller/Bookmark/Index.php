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

namespace WIO\Forum\Controller\Bookmark;

class Index extends \Magento\Framework\App\Action\Action {
  
  protected $_forumUrl;
  protected $_registry;

  protected $_resultPageFactory;  
  protected $_bookmarIds;
  
  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Framework\Registry $registry,  
    \Magento\Framework\View\Result\PageFactory $resultPageFactory, 
    \WIO\Forum\Helper\Url $forumUrl
  ) {
    parent::__construct($context);
    $this->_forumUrl = $forumUrl;
    $this->_registry = $registry;
    $this->_resultPageFactory = $resultPageFactory;
  }
  
  
  public function dispatch(
    \Magento\Framework\App\RequestInterface $request
  ){
    $this->_bookmarIds = $request->getParam($this->getTopicIdsParamName());
    return parent::dispatch($request);
  }
  
  
  public function execute() {
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_BOOKMAR_REGISTRATED, $this->_bookmarIds);
    $resultPage = $this->_resultPageFactory->create();

    $breadcrumbs = $resultPage->getLayout()->getBlock('breadcrumbs');
    $breadcrumbs->addCrumb('forum_home', [
        'label' => __('Forum'),
        'title' => __('Forum'),
        'link' => '/' . $this->_forumUrl->getBaseForumUrl()
            ]
    );
    
    $breadcrumbs->addCrumb('forum_bookmarks', [
        'label' => __('Bookmarks'),
        'title' => __('Bookmarks')
            ]
    );
    
    return $resultPage;
  }  
  
  protected function getTopicIdsParamName() {
    return \WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_IDS;
  }
}