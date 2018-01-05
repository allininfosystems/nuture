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

class Delete extends \Magento\Backend\App\Action {
  
  /**
   * {@inheritdoc}
   */
  protected function _isAllowed() {
    return $this->_authorization->isAllowed('WIO_Forum:forum_topic_delete');
  }

  public function execute() {

    /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
    $resultRedirect = $this->resultRedirectFactory->create();
    $id = $this->getRequest()->getParam('post_id');

    $model = $this->_objectManager->create('WIO\Forum\Model\Post');
    $model->load($id);
    if ($model->getId()) {
      $model->setIsDeleted(1);
      try {
        $model->save();
        $this->messageManager->addSuccess(__('You successfully delete post'));
      } catch (Exception $ex) {
        $this->messageManager->addException($ex, __('Something went wrong while deleteing the post.'));
      }
    }
    return $resultRedirect->setPath('*/*/');
  }
}