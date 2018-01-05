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

namespace WIO\Forum\Controller\Rss;

class Index extends \Magento\Framework\App\Action\Action {

  protected $_forumModel;
  protected $_topicModel;
  protected $_registry;
  
  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \WIO\Forum\Model\ForumFactory $forumModel,
    \WIO\Forum\Model\TopicFactory $topicModel,
    \Magento\Framework\Registry $registry
  ) {
    parent::__construct($context);
    $this->_forumModel = $forumModel;
    $this->_registry = $registry;
    $this->_topicModel = $topicModel;
  }
  
  public function dispatch(
    \Magento\Framework\App\RequestInterface $request
  ){
    $this->registerObjects($request);
    return parent::dispatch($request);
  }
  
  public function execute() {
    $this->_view->loadLayout();
    $this->getResponse()
            ->setHeader('Content-type', 'text/xml; charset=UTF-8')
            ->setBody(
                    $this->_view->getLayout()->getBlock('forum.rss.feed')->toHtml()
    );
  }
  
  protected function registerObjects($request) {
    $topic_id = $request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME);
    if ($topic_id) {
      $topicModel = $this->_topicModel->create()->load($topic_id);
      if($topicModel 
              && $topicModel->getId()
              && !$topicModel->getIsDeleted()
              && $topicModel->getStatus()) {
        $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_REGISTRATED_TOPIC, $topicModel);
        return;
      }
    }
    return $this->registerForums($request);
  }

  protected function registerForums($request) {
    $forumCollection = $this->_forumModel->create()->getCollection();
    $forumCollection->forumsOnly()
            ->enabledOnly()
            ->addStoreFilterToCollection();
    
    if($forum_id = $request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_ID_PARAM_NAME)) {
      $forumCollection->getById($forum_id);
    }
    
    $this->_registry->register(\WIO\Forum\Helper\Constant::WIO_FORUM_BOOKMAR_REGISTRATED, $forumCollection);
  }
}
