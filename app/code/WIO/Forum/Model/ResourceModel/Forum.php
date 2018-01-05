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
class Forum extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

  protected $_storeManager;

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
  }

  protected function _afterSave(\Magento\Framework\Model\AbstractModel $object) {
    $oldAccess = $this->loadAccessGroups($object->getId());
    $newAccess = (array) $object->getCustomerGroupId();

    $insert = array_diff($newAccess, $oldAccess);
    $delete = array_diff($oldAccess, $newAccess);

    if ($delete) {
      $this->deleteAccessGroups($object->getId(), $delete);
    }

    if ($insert) {
      $this->createAccessGroups($object->getId(), $insert);
    }

    return parent::_afterSave($object);
  }

  protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object) {
    if ($object->getId()) {
      $groups = $this->loadAccessGroups($object->getId());
      $object->setData('customer_group_id', $groups);
    }

    return parent::_afterLoad($object);
  }

  protected function loadAccessGroups($forum_id) {

    $connection = $this->getConnection();

    $select = $connection->select()->from(
                    $this->getTable('forum_access'), 'group_id'
            )->where(
            'forum_id = ?', (int) $forum_id
    );

    return $connection->fetchCol($select);
  }

  protected function createAccessGroups($forum_id, $insert_arr) {
    $table = $this->getTable('forum_access');
    $data = [];
    foreach ($insert_arr as $group_id) {
      $data[] = ['forum_id' => $forum_id, 'group_id' => $group_id];
    }
    $this->getConnection()->insertMultiple($table, $data);
  }

  protected function deleteAccessGroups($forum_id, $arr_delete = array()) {
    $table = $this->getTable('forum_access');
    $where = ['forum_id = ?' => (int) $forum_id, 'group_id IN (?)' => $arr_delete];
    $this->getConnection()->delete($table, $where);
  }

}
