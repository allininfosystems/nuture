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

class MassDelete extends \Magento\Backend\App\Action {
  
  /**
   * {@inheritdoc}
   */
  protected function _isAllowed() {
    return $this->_authorization->isAllowed('WIO_Forum:post_delete');
  }
  
  public function execute() {
    $data = $this->getRequest()->getPostValue();
    $ids = $this->getIdsDelete($data);
    $resultRedirect = $this->resultRedirectFactory->create();
    $postsDeleted = 0;
    if (is_array($ids)) {
      try {
        foreach ($ids as $id) {
          $model = $this->_objectManager->create('WIO\Forum\Model\Post');
          $model->load($id);
          if ($model->getId()) {
            $model->setIsDeleted(1);
            $model->save();
          }
          $postsDeleted++;
        }
        $this->messageManager->addSuccess(__('Total %1 post(s) deleted', $postsDeleted));
      } catch (Exception $ex) {
        $this->messageManager->addException($ex, __('Something went wrong while deleteing the post.'));
      }
    }

    return $resultRedirect->setPath('*/*/');
  }
  
  protected function getIdsDelete($data) {
    $arrayExcluded = array();
    if(!empty($data['selected'])) {
      return $data['selected'];
    } elseif(!empty($data['excluded'])) {
      $arrayExcluded = $data['excluded'];
    }
    $toDeleteIds = array();
    $allCollection = $this->_objectManager->create('WIO\Forum\Model\Post')->getCollection();
    $allCollection->notDeleted();
    
    foreach($allCollection as $item) {
      $toDeleteIds[] = $item->getId();
    }
    
    $arrayExcluded = is_array($arrayExcluded) ? $arrayExcluded : array();
    return array_diff($toDeleteIds, $arrayExcluded); 
  }
}