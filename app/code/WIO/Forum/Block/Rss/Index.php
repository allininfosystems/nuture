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

namespace WIO\Forum\Block\Rss;

class Index extends \Magento\Framework\View\Element\Template {

  protected $_collection;
  protected $_registry;
  protected $_postModel;
  protected $_feedsArray;
  protected $_forumData;
  protected $_helperUrl;
  
  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, 
    \Magento\Framework\Registry $registry,
    \WIO\Forum\Model\PostFactory $postModel,    
    \WIO\Forum\Helper\Url $helperUrl, 
    \WIO\Forum\Helper\Data $forumData, 
    array $data = array()
  ) {
    parent::__construct($context, $data);
    $this->_registry = $registry;
    $this->_postModel = $postModel;
    $this->_forumData = $forumData;
    $this->_helperUrl = $helperUrl;
  }
  
  protected function _toHtml() {
    $this->initCollection();
    $this->initFeedHeader();
    if ($this->_collection->getSize()) {
      foreach($this->_collection as $_post) {
        $this->_feedsArray['entries'][] = array(
            'title' => __('Forum Post with ID: %1', $_post->getId()),
            'link'  => $this->_helperUrl->getLatestViewUrl($_post->getId()),
            'lastUpdate' => strtotime($_post->getCreatedTime()),
            'description' => $_post->getPostOrig() ? $_post->getPostOrig() : 'none',
            'content' => $_post->getPost()
        );
      }
    }
    
    $rssFeedFromArray = \Zend_Feed::importArray($this->_feedsArray, 'rss');
    return $rssFeedFromArray->saveXML();
  }

  protected function initFeedHeader() {
    $this->_feedsArray = array(
        'title' => $this->_forumData->getForumTitleFront(),
        'link'  => $this->_helperUrl->getBaseForumUrl(),
        'description' => $this->_forumData->getForumTitleFront(),
        'charset' => 'UTF-8',
        'entries' => array()
    );
  }
  
  protected function initCollection() {
    $forumIds = $this->getForumsIds();
    $topicId  = $this->getTopicId();
    $this->_collection = $this->_postModel->create()->getCollection();
    $this->_collection->notDeleted()
            ->enabledOnly();
    if($forumIds) {
      $this->_collection->addFieldToFilter('forum_id', array(
          'in' => $forumIds
      ));
    }
    if($topicId) {
      $this->_collection->byParent($topicId);
    }
  }
  
  protected function getForumsIds() {
    $forumIds = null;
    $forumCollection = $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_BOOKMAR_REGISTRATED);
    if($forumCollection && $forumCollection->getSize()) {
      $forumIds = array();
      foreach($forumCollection as $forum) {
        $forumIds[] = $forum->getId();
      }
    }
    return $forumIds;
  }
  
  protected function getTopicId() {
    $topicModel = $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_REGISTRATED_TOPIC);
    if($topicModel) {
      return $topicModel->getId();
    }
  }
}
