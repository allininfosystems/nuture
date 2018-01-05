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

namespace WIO\Forum\Controller\Adminhtml\Post;

class NewAction extends \WIO\Forum\Controller\Adminhtml\Index {

  /**
   * Create new forum
   *
   * @return \Magento\Framework\Controller\ResultInterface
   */
  public function execute() {
    /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
    $resultForward = $this->resultForwardFactory->create();
    return $resultForward->forward('edit');
  }

}
