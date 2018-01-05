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

namespace WIO\Forum\Block\Search\Index;

class Grid extends \WIO\Forum\Block\Search\Index {

  protected $search_block_begin = '<span class="forum-search-block-selected">';
  protected $search_block_end = '</span>';
  
  protected $_topicsLoaded;

  protected function _prepareLayout() {
    
    $this->_collection = $this->getSearchCollection();
    if(!$this->getLayout()->getBlock('wioforum.forum.search.pager') 
            && $this->_collection->getSize()){
      parent::_prepareLayout();
      $pager = $this->getLayout()->createBlock(
        'Magento\Theme\Block\Html\Pager', 'wioforum.forum.search.pager'
      );
      $pager->setLimit($this->getPageLimit())
              ->setShowAmounts(true)
              ->setCollection($this->_collection);
      $pager->setAvailableLimit(array(10=>10,30=>30,50=>50));
      $this->setChild('wioforum.forum.search.pager', $pager);
    }
    return $this;
  }

  public function getSortUrl($type = 'asc') {
    return $this->getUrl(\WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/search/index', array(
                \WIO\Forum\Helper\Constant::WIO_FORUM_SORTING => $type,
                \WIO\Forum\Helper\Constant::WIO_FORUM_PAGE_NUM => 1
    ));
  }

  public function getCollection() {
    return $this->_collection;
  }

  public function setMarkPost($_post) {
    $search_val = $this->getSearchPhrase();
    $pattern = '#(?!<.*)(?<!\w)(\w*)(' . $search_val . ')(\w*)(?!\w|[^<>]*(?:</s(?:cript|tyle))?>)#is';
    return preg_replace($pattern, '$1' . $this->search_block_begin . '$2' . $this->search_block_end . '$3', $_post);
    return $_post;
  }

  public function getPagerHtml() {
    return $this->getChildHtml('wioforum.forum.search.pager');
  }
  
  public function getTopic($topic_id) {
    if(empty($this->_topicsLoaded[$topic_id])) {
      $topicModel = $this->_topicModel->create()->load($topic_id);
      if($topicModel && $topicModel->getId() && $topicModel->getParentId()){
        $topicModel->setParentForum($this->getParentForum($topicModel->getParentId()));
      }
      $this->_topicsLoaded[$topic_id] = $topicModel;
    }
    
    return $this->_topicsLoaded[$topic_id];
  }
  
  public function getTopicViewUrl($topicModel) {
    return $this->_helperUrl->getTopicUrl($topicModel->getParentForum(), $topicModel);
  }
  
  public function getViewPostUrl($post_id) {
    return $this->_helperUrl->getLatestViewUrl($post_id);
  }
}
