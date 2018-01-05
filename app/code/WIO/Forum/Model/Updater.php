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

class Updater {

  protected $_topicFactory;
  protected $_forumFactory;
  protected $_postFactory;

  public function __construct(
    TopicFactory $topicFactory, 
    ForumFactory $forumFactory, 
    PostFactory $postFactory
  ) {
    $this->_forumFactory = $forumFactory;
    $this->_topicFactory = $topicFactory;
    $this->_postFactory = $postFactory;
  }
  
  public function updateParentTopic($_topicId) {
    $topicModel = $this->_topicFactory->create();
    $topicModel->load($_topicId);
    if(!$topicModel->getId() || $topicModel->getIsDeleted()) {
      return;
    }
    if(!$topicModel->getIsCategory()) {
      $postCollection = $this->_postFactory->create()->getCollection();
      $postCollection->notDeleted()->enabledOnly()->byParent($_topicId);
      $postCollection->setOrder(\WIO\Forum\Helper\Constant::WIO_FORUM_CREATED_TIME_SORT_FIELD, 'desc');
      
      $topicModel->setTotalPosts($postCollection->count());
      $firstPostItem = $postCollection->getFirstItem();
      
      if($firstPostItem->getId()) {
        $topicModel->setLastPostId($firstPostItem->getId());
        $topicModel->setTmpstPost($firstPostItem->getTmpst());
      }else{
        $topicModel->setLastPostId(0);
        $topicModel->setTmpstPost(0);
      }
    }else{
      $topicCollection = $this->_topicFactory->create()->getCollection();
      $topicCollection->enabledOnly()->notDeleted()
              ->byParent($topicModel->getId());
      $topicCollection->setOrder(\WIO\Forum\Helper\Constant::WIO_FORUM_TIMESTAMP_TOPIC_POST, 'desc');
      $firstTopicItem = $topicCollection->getFirstItem();
      $totalPosts = $this->getTotalTopicPosts($topicModel);
      if($firstTopicItem->getId()) {
        $topicModel->setTmpstPost($firstTopicItem->getTmpstPost());
        $topicModel->setLastPostId($firstTopicItem->getLastPostId());
      }else{
        $topicModel->setTmpstPost(0);
        $topicModel->setLastPostId(0);
      }
      $topicModel->setTotalPosts($totalPosts);
      $topicModel->setTotalTopics($topicCollection->count());
    }
    $topicModel->save();
    /*
    $parentId = $topicModel->getParentId();
    if($parentId) {
      $this->updateParentTopic($parentId);
    }
    */
  }
  
  protected function getTotalTopicPosts($topicModel) {
    $topicCollection = $this->_topicFactory->create()->getCollection();
    $topicCollection->getSelect()
        ->reset(\Zend_Db_Select::COLUMNS)
        ->columns('SUM(total_posts) as total_posts');
    
    $topicCollection->enabledOnly()->notDeleted()
              ->byParent($topicModel->getId());
    $item = $topicCollection->getFirstItem();
    return $item->getTotalPosts();
  }
}
