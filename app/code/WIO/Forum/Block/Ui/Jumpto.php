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

class Jumpto extends \WIO\Forum\Block\Forum\Index {

  protected function _prepareLayout() {
    parent::_prepareLayout();
    return $this;
  }

  public function getIsEnabled() {
    return $this->_forumData->getIsJumpToEnabled();
  }
  
  public function getForumViewUrl($forumObj){
    return $this->_helperUrl->getForumUrl($forumObj);
  }
}
