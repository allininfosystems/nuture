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

namespace WIO\Forum\Controller\Adminhtml\Moderator;

class Customers extends \WIO\Forum\Controller\Adminhtml\Index {

  public function execute() {
    $this->_view->loadLayout();
    $this->_view->renderLayout();
  }

}
