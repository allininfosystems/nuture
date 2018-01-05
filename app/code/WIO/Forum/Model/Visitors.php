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
use Magento\Framework\Stdlib\DateTime\DateTime;

class Visitors{
  
  protected $_visitor;
  protected $_registry;
  protected $_storeManager;
  protected $_date;
  
  public function __construct(
    VisitorFactory $visitor,
    \Magento\Framework\Registry $registry,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    DateTime $date
  ) {
    $this->_visitor = $visitor;
    $this->_registry = $registry;
    $this->_storeManager = $storeManager;
    $this->_date = $date;
  }
  
  public function registerVisitor() {
    $topicId = NULL;
    $parentId = NULL;
    $currentObject = $this->getCurrentObject();
    if ($currentObject) {
        $topicId = $currentObject->getId();
        $parentId = $currentObject->getParent_id();
    } 
    $visitor_id = $this->getVisitorIdBySession($this->getCustomerSessionId());
    $visitorModel = $this->_visitor->create();
    $visitorModel->setId($visitor_id)
      ->setSystemUserId($this->getCustomerId())
      ->setSessionId($this->getCustomerSessionId())
      ->setTopicId($topicId)
      ->setTimeVisited($this->_date->gmtDate())
      ->setParentId($parentId)
      ->setStoreId($this->getStoreId());
    $visitorModel->save();
    
    $this->unsetOldEntries();
  }
  
  public function getCurrentObject(){
    $topic = $this->getCurrentTopicView();
    if($topic) {
      return $topic;
    }
    $forum = $this->getCurrentForumView();
    return $forum;
  }

  // move to cron
  public function unsetOldEntries() {
    $_5min_back = date('Y-m-d H:i:s', strtotime($this->_date->gmtDate()) - 300);
    $mc = $this->_visitor->create()->getCollection();
    $mc->getSelect()->where('time_visited<?', $_5min_back);
    foreach ($mc as $del) {
        $this->_visitor->create()
          ->setId($del->getId())
          ->delete();
    }
  }
  
  protected function getVisitorIdBySession($sessId){
    $visitorModel = $this->_visitor->create();
    $id = $visitorModel->load($sessId, 'session_id')->getId();
    return $id;
  }
  
  protected function getCustomerId(){
    $customer = $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION);
    return $customer->getId();
  }
  
  protected function getCustomerSessionId() {
    //simple but working
    return session_id();
  }
  
  protected function getCurrentForumView() {
    $forum = $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_FORUM);
    return $forum ? $forum : null;
  }
  
  protected function getCurrentTopicView() {
    $topic = $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_TOPIC);
    return $topic ? $topic : null;
  }
  
  protected function getStoreId(){
    return $this->_storeManager->getStore()->getId();
  }
}
