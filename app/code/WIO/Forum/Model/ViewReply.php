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

class ViewReply {

  protected $_postModel;
  protected $_topicModel;
  protected $_session;
  protected $_url;
  protected $_viewRepliesLimit;
  protected $_viewRepliesSort;
  protected $_viewRepliesSortField;
  
  public function __construct(
    PostFactory $postModel,
    TopicFactory $topicModel,
    \WIO\Forum\Helper\Url $url,
    Session $session
  ) {
    $this->_postModel  = $postModel;
    $this->_topicModel = $topicModel;
    $this->_session    = $session;
    $this->_url = $url;
    
    $this->_viewRepliesLimit = \WIO\Forum\Helper\Constant::WIO_FORUM_DEFAULT_POST_PAGE_SIZE;
    $this->_viewRepliesSort  = \WIO\Forum\Helper\Constant::DEFAULT_SORT;
    $this->_viewRepliesSortField = \WIO\Forum\Helper\Constant::WIO_FORUM_CREATED_TIME_SORT_FIELD;
  }

  /**
   * return array(
   *  'postModel->load($post_id)',
   *  'identifier' => $identifier
   * )
   */
  public function getPostArray($post_id) {
    $post = $this->_postModel->create()->load($post_id);
    if($post->getIsDeleted() || !$post->getParentId() || !$post->getId()) {
      return $this->notFound();
    }
    $parentCollection = $this->getParentCollection($post);
    $pageNum = $this->getPageNumber($parentCollection, $post->getId());
    
    return array(
      'identifier' => $this->getPostBlockId() . $post->getId(),
      'post' => $post->getData(),
      'url'  => $this->getPageUrl($post, $pageNum)  
    );
  }
  
  protected function getPageUrl($post, $pageNum) {
    $parentTopic = $this->_topicModel->create()->load($post->getParentId());
    if($parentTopic->getIsDeleted()
            || $parentTopic->getStatus() == 0) {
      return null;
    }
    
    $parentForum = $this->_topicModel->create()->load($parentTopic->getParentId());
    if($parentForum->getIsDeleted()
            || $parentForum->getStatus() == 0) {
      return null;
    }
    return $this->_url->getTopicUrl($parentForum, $parentTopic, array(
      \WIO\Forum\Helper\Constant::WIO_FORUM_PAGE_NUM => $pageNum,
      \WIO\Forum\Helper\Constant::WIO_FORUM_PER_PAGE => $this->_viewRepliesLimit,
      \WIO\Forum\Helper\Constant::WIO_FORUM_SORTING => $this->_viewRepliesSort
    )) . '#' . $this->getPostBlockId() . $post->getId();
  }
  
  protected function getPostBlockId() {
    return \WIO\Forum\Helper\Constant::WIO_FORUM_POST_BLOCK_ID;
  }
  
  protected function getParentCollection($postModel) {
    $parentCollection = $this->_postModel->create()->getCollection();
    $parentCollection->notDeleted()
            ->byParent($postModel->getParentId())
            ->setOrder($this->_viewRepliesSortField, $this->_viewRepliesSort);
    $parentCollection->enabledOnly();
    return $parentCollection;
  }
  
  protected function getPageNumber($parentCollection, $post_id){
    if($parentCollection->getSize() == 1){
      return 1;
    }
    $pagePostsSize = \WIO\Forum\Helper\Constant::WIO_FORUM_DEFAULT_POST_PAGE_SIZE;
    $num = 1;
    $count = 1;
    foreach ($parentCollection as $val) {
      if ($count > $pagePostsSize) {
          $count = 1;
          $num++;
      }
      if ($val->getId() == $post_id) {
          return $num;
      }
      $count++;
    }
    return 1;
  }
  
  protected function notFound() {
    return array (
        'url' => null,
        'post' => null,
        'identifier' => null
      );
  }
  
}
