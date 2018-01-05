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
 * Post model
 */
class Post extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

  
  protected $_topicBeforeSave;
  protected $_updater;
  /**
   * Initialize resource model
   *
   * @return void
   */
  protected function _construct() {
    $this->_updater = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('\WIO\Forum\Model\Updater');
    $this->_init('forum_post', 'post_id');
  }
  
  
  protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object) {
    if(!$object->getId()) { //new post
      if ($object->getCreated_time()) {
        $tmpst = strtotime($object->getCreated_time());
        $object->setTmpst($tmpst);
      }
    }
    $topic_id = $object->getParentId();
    $this->_topicBeforeSave = $topic_id; // recheck on change posts topic owner in admin!
    
    $forum_id = $this->getParentsId($topic_id);
    if ($forum_id !== null) {
      $object->setForumId( $forum_id );
    }
    return parent::_afterSave($object);
  }
  
  
  protected function _afterSave(\Magento\Framework\Model\AbstractModel $object) {
    $topicAfterSave = $object->getParentId();
    if($topicAfterSave != $this->_topicBeforeSave) {
      $this->_updater->updateParentTopic( $this->_topicBeforeSave );
    }
    $this->_updater->updateParentTopic($topicAfterSave);
  }
  
  protected function getParentsId($parent_id = null) {
    if (!$parent_id) {
      return null;
    }
    $connection = $this->getConnection();
    $select = $connection->select()->from(
                    $this->getTable('forum_topic'), array('parent_id', 'topic_id', 'is_category')
            )->where(
            'topic_id = ? AND is_deleted != 1', (int) $parent_id
    );
    $row = $connection->fetchRow($select);
    if($row && $row['is_category'] == 1 && $row['topic_id']){
      return $row['topic_id']; 
    }else if($row['topic_id'] && $row['parent_id']) {
      return $this->getParentsId($row['parent_id']);
    }else{
      return null;
    }
  }
}
