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

class Whoisonline extends \Magento\Framework\View\Element\Template {

  protected $_visitorModel;
  protected $_visitors;
  protected $_forumData;
  
  public function __construct(
  \Magento\Framework\View\Element\Template\Context $context, 
  \WIO\Forum\Model\VisitorFactory $visitorModel, 
  \WIO\Forum\Model\Visitors $visitors,
  \WIO\Forum\Helper\Data $forumData,
  array $data = array()
  ) {
    parent::__construct($context, $data);
    $this->_visitorModel = $visitorModel;
    $this->_visitors = $visitors;
    $this->_forumData = $forumData;
  }
  
  protected function _prepareLayout() {
    parent::_prepareLayout();
    return $this;
  }
  
  public function getTotalUsers() {
    return $this->getCollection()->count();
  }
  
  public function getQuestsOnly() {
    $collection = $this->getCollection();
    $collection->getSelect()
            ->where('system_user_id=0');
    return $collection->count();
  }
  
  public function getLoggedInOnly(){
    $collection = $this->getCollection();
    $collection->getSelect()
            ->where('system_user_id!=0');
    return $collection->count();
  }
  
  public function getIsActive(){
    return $this->_forumData->getIsWhoisonlineEnabled();
  }
  
  protected function getCollection() {
    $collection = $this->_visitorModel->create()->getCollection();
    $collection->addStoreFilterToCollection($this->getStoreId());
    $currentObject = $this->getCurrentObject();
    if($currentObject) {
      if($currentObject->getIsCategory()) {
        $collection->getSelect()->where('parent_id=? OR topic_id=?', $currentObject->getId(), $currentObject->getId());
      }else{
        $collection->getSelect()->where('topic_id=?', $currentObject->getId());
      }
    }
    return $collection;
  }

  protected function getStoreId(){
    return $this->_storeManager->getStore()->getId();
  }
  
  protected function getCurrentObject() {
    return $this->_visitors->getCurrentObject();
  }
}
