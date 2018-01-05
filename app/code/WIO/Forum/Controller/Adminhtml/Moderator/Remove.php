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

class Remove extends \Magento\Backend\App\Action {

  /**
   * {@inheritdoc}
   */
  protected function _isAllowed() {
    return $this->_authorization->isAllowed('WIO_Forum:moderator_delete');
  }

  public function execute() {
    
    $resultRedirect = $this->resultRedirectFactory->create();
    $data = $this->getRequest()->getPostValue();
    $ids = !empty($data['moderator']) ? $data['moderator'] : array();
    $allDeleted = 0;
    try {

      foreach ($ids as $moderator_id) {
        $model = $this->_objectManager->create('WIO\Forum\Model\Moderator');
        $model->load($moderator_id)
                ->delete();
        $allDeleted++;
      }

      $this->messageManager->addSuccess(__('Total %1 moderator(s) deleted', $allDeleted));
    } catch (Exception $ex) {
      $this->messageManager->addException($ex, __('Something went wrong while deleteing the moderator'));
    }

    return $resultRedirect->setPath('*/*/');
  }

}
