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

namespace WIO\Forum\Controller\Post;

class Delete extends \Magento\Framework\App\Action\Action {
  
  protected $_postModel;
  protected $_customerSession;
  protected $_moderatorModel;

  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \Magento\Customer\Model\Session $customerSession, 
    \WIO\Forum\Model\Moderator $moderatorModel,
    \WIO\Forum\Model\PostFactory $postModel
  ) {
    $this->_postModel = $postModel;
    $this->_customerSession = $customerSession;
    $this->_moderatorModel = $moderatorModel;
    parent::__construct($context);
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
    $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
    $post_id = $this->getRequest()->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_ID_PARAM_NAME);
    $post = $this->_postModel->create()->load($post_id);
    if(!$post->getStatus()
            || $post->getIsDeleted()
            || !$this->getIsOwner($post)) {
      return $this->_redirect('');
    }
    $post->setIsDeleted(1);
    $post->save();
    
    $this->messageManager->addSuccess(__('You successfully delete post'));
    //You successfully delete post
    
    $resultRedirect->setUrl($this->_redirect->getRefererUrl());
    return $resultRedirect;
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
}
