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

class Moderator extends \Magento\Framework\Model\AbstractModel{
  
  /**
   * @return void
   */
  protected function _construct() {
    $this->_init('WIO\Forum\Model\ResourceModel\Moderator');
  }
  
  
  public function isModerator($system_user_id) {
    $this->load($system_user_id, 'system_user_id');
    if($this->getId()) {
      return true;
    }
    return false;
  }
}