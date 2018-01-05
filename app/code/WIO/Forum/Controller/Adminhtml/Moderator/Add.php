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

class Add extends \Magento\Backend\App\Action {

  /**
   * {@inheritdoc}
   */
  protected function _isAllowed() {
    return $this->_authorization->isAllowed('WIO_Forum:moderator_add');
  }

  public function execute() {

    $resultRedirect = $this->resultRedirectFactory->create();
    $data = $this->getRequest()->getPostValue();
    $ids = !empty($data['customer']) ? $data['customer'] : array();

    $allAssigned = 0;
    try {

      foreach ($ids as $id) {
        $model = $this->_objectManager->create('WIO\Forum\Model\Moderator');
        $model->load($id, 'system_user_id');
        if($model->getId()) {
          continue;
        }
        $model->setSystemUserId($id);
        $model->save();
        $allAssigned++;
        
      }
    $this->messageManager->addSuccess(__('Total %1 moderator(s) assigned', $allAssigned));
    } catch (Exception $ex) {
      $this->messageManager->addException($ex, __('Something went wrong while assign moderator'));
    }

    return $resultRedirect->setPath('*/*/');
  }

}
