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

namespace WIO\Forum\Model\ResourceModel;

/**
 * Forum model
 */
class Topic extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

  
  protected $_updater;
  protected $_topicBeforeSave;
  
  public function __construct(
  \Magento\Framework\Model\ResourceModel\Db\Context $context, \Magento\Store\Model\StoreManagerInterface $storeManager, $connectionName = null
  ) {
    parent::__construct($context, $connectionName);
    $this->_storeManager = $storeManager;
  }

  /**
   * Initialize resource model
   *
   * @return void
   */
  protected function _construct() {
    $this->_init('forum_topic', 'topic_id');
    $this->_updater = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('\WIO\Forum\Model\Updater');
  }

  
  protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object) {
    $topic_id = $object->getParentId();
    $this->_topicBeforeSave = $topic_id; // recheck on change posts topic owner in admin!
    
    return parent::_afterSave($object);
  }
  
  protected function _afterSave(\Magento\Framework\Model\AbstractModel $object) {
    $topicAfterSave = $object->getParentId();
    if($topicAfterSave != $this->_topicBeforeSave) {
      $this->_updater->updateParentTopic( $this->_topicBeforeSave );
    }
    $this->_updater->updateParentTopic($topicAfterSave);
  }
}
