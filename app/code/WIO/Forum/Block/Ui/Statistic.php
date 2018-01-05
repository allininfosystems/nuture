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

namespace WIO\Forum\Block\Ui;

class Statistic extends \Magento\Framework\View\Element\Template {

  protected $_forumModel;
  protected $_topicModel;
  protected $_postModel;
  protected $_forumData;
  
  public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, 
    \WIO\Forum\Model\ForumFactory $forumModel, 
    \WIO\Forum\Model\TopicFactory $topicModel, 
    \WIO\Forum\Model\PostFactory $postModel,
    \WIO\Forum\Helper\Data $forumData,
    array $data = array()
  ) {
    $this->_postModel = $postModel;
    $this->_topicModel = $topicModel;
    $this->_forumModel = $forumModel;
    $this->_forumData = $forumData;
    parent::__construct($context, $data);
  }
  
  protected function _prepareLayout() {
    parent::_prepareLayout();
    return $this;
  }
  
  public function getTotalForums() {
    $collection = $this->_forumModel->create()->getCollection();
    return $collection->forumsOnly()->enabledOnly()->count();
  }
  
  public function getTotalTopics() {
    $collection = $this->_topicModel->create()->getCollection();
    return $collection->topicsOnly()->enabledOnly()->count();
  }
  
  public function getTotalPosts() {
    $collection = $this->_postModel->create()->getCollection();
    return $collection->notDeleted()->enabledOnly()->count();
  }
  
  public function getTotalUsers(){
    $collection = $this->_postModel->create()->getCollection();
    $collection->notDeleted()->enabledOnly();
    $collection->getSelect()->group('system_user_id');
    return $collection->count(); 
  }
  
  public function getIsActive(){
    return $this->_forumData->getIsStatisticEnabled();
  }
}
