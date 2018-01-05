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
 * Notification model
 */
class Notification extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

  protected function _construct() {
    $this->_init('forum_notification', 'notify_id');
  }

  protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object) {
    
    if (!$object->getId()) { //new notifiction
      $hash = $this->getHash();
      $object->setHash($hash);
    }
    return parent::_afterSave($object);
  }
  
  protected function getHash(){
    return uniqid();
  }

}
