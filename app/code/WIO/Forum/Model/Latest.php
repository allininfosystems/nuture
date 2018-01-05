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

namespace WIO\Forum\Model;

class Latest {

  protected $_topicFactory;
  protected $_postFactory;

  public function __construct(
    TopicFactory $topicFactory, 
    PostFactory $postFactory
  ) {
    $this->_topicFactory = $topicFactory;
    $this->_postFactory = $postFactory;
  }

  public function getLatestDetails($_postId) {
    $postModel = $this->_postFactory->create();
    $postModel->load($_postId);
    if(!$postModel->getId() 
            || $postModel->getIsDeleted() 
            || !$postModel->getStatus()
            || !$postModel->getParentId()) {
      return false;
    }
    
    $topicModel = $this->_topicFactory->create();
    $topicModel->load($postModel->getParentId());
    if(!$topicModel->getId() 
            || $topicModel->getIsDeleted() 
            || !$topicModel->getStatus()) {
      return false;
    }
    
    return array(
      'parent_topic' => $topicModel,
      'post' => $postModel
    );
  }
  
  
}