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

namespace WIO\Forum\Model\ResourceModel\Post;

use \WIO\Forum\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection {
  
  /**
   * @var string
   */
  protected $_idFieldName = 'post_id';
  
  /**
   * Define resource model
   *
   * @return void
   */
  protected function _construct() {
    $this->_init('WIO\Forum\Model\Post', 'WIO\Forum\Model\ResourceModel\Post');
    /* $this->_map['fields']['store'] = 'store_table.store_id'; */
  }
  
  protected function _beforeLoad() {
    parent::_beforeLoad();
    $this->notDeleted();
    return $this;
  }

  public function notDeleted() {
    $select = $this->getSelect();
    $select->where('is_deleted=?', 0);
    return $this;
  }
  
  
  public function byParent($parent_id){
    $select = $this->getSelect();
    $select->where('parent_id=?', $parent_id);
    return $this;
  }
  
  public function enabledOnly(){
    $select = $this->getSelect();
    $select->where('status=?', 1);

    return $this;
  }
  
  public function getUserPosts($userId){
    $this->enabledOnly()->notDeleted();
    $select = $this->getSelect();
    $select->where('system_user_id=?', $userId);
    
    return $this;
  }
}
