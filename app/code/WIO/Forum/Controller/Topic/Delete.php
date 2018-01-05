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

class Delete extends \Magento\Framework\App\Action\Action {

  protected $_postModel;
  protected $_topicModel;
  protected $_customerSession;
  protected $_forumData;
  protected $_moderatorModel;
  
  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Customer\Model\Session $customerSession, 
    \WIO\Forum\Model\TopicFactory $topicModel,
    \WIO\Forum\Helper\Data $forumData,
    \WIO\Forum\Model\Moderator $moderatorModel,
    \WIO\Forum\Model\PostFactory $postModel      
  ) {
    $this->_postModel = $postModel;
    $this->_topicModel = $topicModel;
    $this->_moderatorModel = $moderatorModel;
    $this->_customerSession = $customerSession;
    $this->_forumData = $forumData;
    parent::__construct($context);
  }
  
  public function dispatch(
  \Magento\Framework\App\RequestInterface $request
  ) {
    if (!$this->_customerSession->isLoggedIn()) {
      $this->_customerSession->setAfterAuthUrl($this->_url->getCurrentUrl());
      $this->_customerSession->authenticate();
    }

    return parent::dispatch($request);
  }

  public function execute() {
    $topic_id = $this->getRequest()->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME);
    $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

    $topic = $this->_topicModel->create()->load($topic_id);
  
    if(!$topic->getStatus()
            || $topic->getIsDeleted()
            || !$this->getIsOwner($topic)) {
      return $this->_redirect('');
    }
    $totlaPostsDeleted = $this->deletPosts($topic);
    $topic->setIsDeleted(1);
    $topic->save();
    $this->messageManager->addSuccess(__('You successfully delete topic'));
    
    if($totlaPostsDeleted) {
      $this->messageManager->addSuccess(__('Total %1 post(s) deleted', $totlaPostsDeleted));
    }
    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    return $resultRedirect;
  }
  
  protected function deletPosts($topic){
    $collection = $this->_postModel->create()->getCollection();
    $collection->getSelect()->where('parent_id=? AND is_deleted=0', $topic->getId());
    $count = 0;
    foreach($collection as $itemDel){
      $model = $this->_postModel->create()->load($itemDel->getId());
      $model->setIsDeleted(1);
      $model->save();
      $count++;
    }
    return $count;
  }
  
  
  protected function getIsModerator() {
    if($this->_customerSession->getId()) {
      return $this->_moderatorModel->isModerator($this->_customerSession->getId());
    }
  }
  
  protected function getIsOwner($object) {
    
    if($this->getIsModerator()) {
      return true;
    }
    if($this->_customerSession->getId() == $object->getSystemUserId()) {
      return true;
    }
  }
  
  protected function getIsCustomerAllowedDeleteTopics() {
    return $this->_forumData->getIsCustomerAllowedDeleteTopics();
  }
}
