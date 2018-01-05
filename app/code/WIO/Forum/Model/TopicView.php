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

class TopicView{
  
  protected $_topicModel;
  protected $_session;
  protected $_registry;
  
  public function __construct(
    \WIO\Forum\Model\TopicFactory $topicModel,
    \Magento\Framework\Registry $registry, 
    \WIO\Forum\Model\Session $session        
  ){
    $this->_topicModel = $topicModel;
    $this->_session = $session;
    $this->_registry = $registry;
  }
          
  public function updateViews() {
    $topic = $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_TOPIC);
    if(!$topic || !is_object($topic)) {
      return;
    }
    if($this->getIsSetTotalViews($topic->getId())){
      $this->setTotalViewsTopic($topic->getId());
    }
  }
  
  protected function getTotalViews(){
    $totalViews = $this->_session->getTopicViews();
    if(!$totalViews) {
      $totalViewsArr = array();
    }else{
      $totalViewsArr = unserialize($totalViews);
    }
    return $totalViewsArr;
  }
  
  protected function setTotalViews($totalViewsArr){
    $totalViews = serialize($totalViewsArr);
    $this->_session->setTopicViews($totalViews);
  }
  
  protected function getIsSetTotalViews( $topic_id ) {
    $totalViewsArr = $this->getTotalViews();
    if(!in_array($topic_id, $totalViewsArr)){
      $totalViewsArr[] = $topic_id;
      $this->setTotalViews($totalViewsArr);
      return true;
    }
  }
  
  protected function setTotalViewsTopic($topic_id){
    $topic = $this->_topicModel->create()->load($topic_id);
    if(!$topic->getId()) {
      return;
    }
    $totalViews  = $topic->getTotalViews();
    $totalViews += 1;
    $topic->setTotalViews($totalViews);
    $topic->save();
  } 
}
