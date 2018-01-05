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

namespace WIO\Forum\Block\Search;

class Index extends \Magento\Framework\View\Element\Template {

  protected $_registry;
  protected $_forumData;
  protected $_topicModel;
  protected $_postModel;
  protected $_helperUrl;
  
  protected $_type;
  protected $_collection;
  protected $_forumsLoaded;
  
  protected $_customerSession;
  
  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, 
    \WIO\Forum\Helper\Data $forumData,
    \Magento\Framework\Registry $registry,    
    \Magento\Customer\Model\Session $customerSession,
    \WIO\Forum\Helper\Url $helperUrl, 
    \WIO\Forum\Model\PostFactory $postModel, 
    \WIO\Forum\Model\TopicFactory $topicModel,
    array $data = array()
  ) {
    $this->_forumData  = $forumData;
    $this->_registry   = $registry;
    $this->_postModel  = $postModel;
    $this->_helperUrl  = $helperUrl;
    $this->_topicModel = $topicModel;
    $this->_customerSession = $customerSession;
    parent::__construct($context, $data);
    $this->_type = $this->getSearchType();
  }
  
  protected function _prepareLayout() {
      
    $pageLayout = $this->_forumData->getForumPageLayout();
    if ($pageLayout) {
        $this->pageConfig->setPageLayout($pageLayout);
    }  
    $this->pageConfig->getTitle()->set(__('Forum'));  
    return parent::_prepareLayout();
  }
  
  public function getTimeAccordingToTimeZone($dateTime) {
    return $this->_forumData->getTimeAccordingToTimeZone($dateTime);
  }

  public function getSearchPhrase() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_REGISTRATED);
  }
  
  public function getSearchType() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE);
  }
  
  public function getIsSearchPost() {
    if($this->getSearchType() == \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST) {
      return true;
    }
  }
  
  public function getIsSearchTopic() {
    if($this->getSearchType() == \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_TOPIC) {
      return true;
    }
  }
  
  public function getSearchByPost() {
    return $this->getUrl(
            \WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/search/index',
            array(
            \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE => \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST
            ));
  }
  
  public function getSearchByTopic() {
    return $this->getUrl(
            \WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/search/index',
            array(
            \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE => \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_TOPIC
            ));
  }
  
  public function getSortType() {
    $constant = ($this->_type == \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST
            ? \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST_SORT 
            : \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_TOPIC_SORT);
    
    return  $this->_registry->registry($constant);
  }
  
  public function getParentForum($forum_id) {
    if(empty($this->_forumsLoaded[$forum_id])) {
      $forumModel = $this->_topicModel->create()->load($forum_id);
      $this->_forumsLoaded[$forum_id] = $forumModel; 
    } 
    
    return $this->_forumsLoaded[$forum_id];
  }
  
  protected function getSearchCollection() {
    if($this->getIsSearchPost()) {
      return $this->searchByPosts();
    }
    return $this->searchByTopics();
  }
  
  protected function searchByPosts() {
    $customerGroupId = $this->_customerSession->getCustomerGroupId();
    
    $postCollection = $this->_postModel->create()->getCollection();
    $postCollection->notDeleted()
      ->enabledOnly();
    $postCollection->setOrder($this->getSortField(), $this->getSortType());
    
    $postCollection->addFieldToFilter('post_orig',array('like' => '%' . $this->getSearchPhrase() . '%'));
    
    $myTable = $postCollection->getTable('forum_access');;
    $postCollection->getSelect()->joinLeft(
                    ["access_table" => $myTable], 
                    "main_table.forum_id = access_table.forum_id", 
                    ["access_group" => "access_table.group_id"]
                )->group('main_table.post_id');
    
    $postCollection->getSelect()->where('access_table.group_id = ? OR access_table.group_id IS NULL', $customerGroupId);
    
    return $postCollection;
  }
  
  protected function searchByTopics() {
    $customerGroupId = $this->_customerSession->getCustomerGroupId();
    
    $topicCollection = $this->_topicModel->create()->getCollection();
    
    $topicCollection->enabledOnly()
            ->topicsOnly()
            ->notDeleted();
    $topicCollection->setOrder($this->getSortField(), $this->getSortType());
    $topicCollection->addFieldToFilter('title',array('like' => '%' . $this->getSearchPhrase() . '%'));

    
    $myTable = $topicCollection->getTable('forum_access');;
    $topicCollection->getSelect()->joinLeft(
                    ["access_table" => $myTable], 
                    "main_table.parent_id = access_table.forum_id", 
                    ["access_group" => "access_table.group_id"]
                )->group('main_table.topic_id');
    
    $topicCollection->getSelect()->where('access_table.group_id = ? OR access_table.group_id IS NULL', $customerGroupId);
    
    //var_dump($topicCollection->getSelect()->__toString());
    return $topicCollection;
  }
  
  protected function getSortField() {
    return \WIO\Forum\Helper\Constant::WIO_FORUM_CREATED_TIME_SORT_FIELD;
  }
  
  protected function getPageNum() {
    $constant = ($this->_type == \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST
            ? \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST_PAGE 
            : \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_TOPIC_PAGE);
   
    return  $this->_registry->registry($constant);
  }
  
  protected function getPageLimit() {
    $constant = ($this->_type == \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST
            ? \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_POST_LIMIT 
            : \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_TYPE_TOPIC_LIMIT);
    
    return  $this->_registry->registry($constant);
  }
}
