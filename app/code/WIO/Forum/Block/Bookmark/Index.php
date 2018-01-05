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

namespace WIO\Forum\Block\Bookmark;

class Index extends \Magento\Framework\View\Element\Template {
  
  protected $_registry;
  protected $_forumModel;
  protected $_topicModel;
  protected $_forumData;
  protected $_helperUrl;
  
  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context,
    \Magento\Framework\Registry $registry, 
    \WIO\Forum\Model\TopicFactory $topicModel,
    \WIO\Forum\Model\ForumFactory $forumModel,
    \WIO\Forum\Helper\Data $forumData,
    \WIO\Forum\Helper\Url $helperUrl, 
    array $data = array()
  ) {
    parent::__construct($context, $data);
    $this->_registry = $registry;
    $this->_forumData = $forumData;
    $this->_topicModel = $topicModel;
    $this->_forumModel = $forumModel;
    $this->_helperUrl = $helperUrl;
  }
  
  protected function _prepareLayout() {
    $this->pageConfig->getTitle()->set( __('Forum Bookmarks'));
    
    $pageLayout = $this->_forumData->getForumPageLayout();
    if ($pageLayout) {
        $this->pageConfig->setPageLayout($pageLayout);
    }  
    
    return parent::_prepareLayout();
  }
  
  public function getTimeAccordingToTimeZone($dateTime) {
    return $this->_forumData->getTimeAccordingToTimeZone($dateTime);
  }
  
  public function getItemUrl($parentObj, $childObj = null) {
    if($childObj === null) {
      return $this->_helperUrl->getForumUrl($parentObj);
    }
    return $this->_helperUrl->getTopicUrl($parentObj, $childObj);
  }
  
  public function getBookmarksIds(){
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_BOOKMAR_REGISTRATED); 
  }
  
  public function getItem($topic_id){
    $topic = $this->_topicModel->create()->load($topic_id);
    if(!$topic->getId()
            || $topic->getIsDeleted()
            || !$topic->getStatus()) {
      return;
    }
    $forum = $this->_forumModel->create()->load($topic->getParentId());
    if(!$forum->getId()
            || $forum->getIsDeleted()
            || !$forum->getStatus()) {
      return;
    }
    $topic->setParentForum($forum);
    return $topic;
  }
}
