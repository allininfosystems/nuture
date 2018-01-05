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

namespace WIO\Forum\Model\ResourceModel\Forum;

use \WIO\Forum\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection {

  /**
   * @var string
   */
  protected $_idFieldName = 'topic_id';

  /**
   * Define resource model
   *
   * @return void
   */
  protected function _construct() {
    $this->_init('WIO\Forum\Model\Forum', 'WIO\Forum\Model\ResourceModel\Forum');
    //$this->_map['fields']['store'] = 'main_table.store_id';
  }

  protected function _beforeLoad() {
    parent::_beforeLoad();
    return $this->forumsOnly();
  }

  public function forumsOnly() {
    $select = $this->getSelect();
    $select->where('is_category=?', 1)->where('is_deleted=?', 0);
    return $this;
  }

  public function enabledOnly() {
    $select = $this->getSelect();
    $select->where('status=?', 1);
    return $this;
  }
  
  public function getById($id){
    $select = $this->getSelect();
    $select->where('topic_id=?', $id);
    return $this;
  }

  public function toOptionArray() {
    return parent::_toOptionArray('topic_id', 'title');
  }

  public function addStoreFilterToCollection() {
    $storeId = intval($this->storeManager->getStore()->getId());
    if( $storeId ) {
      $this->getSelect()->where('main_table.store_id = 0
        OR main_table.store_id = \'' . $storeId . '\'
        OR main_table.store_id IS NULL
      ');
    }
    return $this;
  }

  public function getSelectOptions() {
    $options = $this->toOptionArray();
    $selectOptions = array();
    if (is_array($options) && count($options)) {
      foreach ($options as $option) {
        $selectOptions[$option['value']] = $option['label'];
      }
    }
    return $selectOptions;
  }

}
