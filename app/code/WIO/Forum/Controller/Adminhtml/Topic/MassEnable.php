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

namespace WIO\Forum\Controller\Adminhtml\Topic;

class MassEnable extends \Magento\Backend\App\Action {

  /**
   * {@inheritdoc}
   */
  protected function _isAllowed() {
    return $this->_authorization->isAllowed('WIO_Forum:topic_status');
  }

  public function execute() {
    $data = $this->getRequest()->getPostValue();
    $ids = $this->getIdsStatus($data);
    $resultRedirect = $this->resultRedirectFactory->create();
    $topicsStatus = 0;
    $postsStatus = 0;
    if (is_array($ids)) {
      try {
        foreach ($ids as $id) {
          $model = $this->_objectManager->create('WIO\Forum\Model\Topic');
          $model->load($id);
          if ($model->getId()) {
            $model->setStatus(1);
            $postsStatus += $this->statusAllPosts($model->getId());
            $model->save();
          }
          $topicsStatus++;
        }
        $this->messageManager->addSuccess(__('Total %1 topic(s) enabled', $topicsStatus));
        $this->messageManager->addSuccess(__('Total %1 post(s) enabled', $postsStatus));
      } catch (Exception $ex) {
        $this->messageManager->addException($ex, __('Something went wrong while enabling the topic.'));
      }
    }

    return $resultRedirect->setPath('*/*/');
  }

  protected function getIdsStatus($data) {
    $arrayExcluded = array();
    if(!empty($data['selected'])) {
      return $data['selected'];
    } elseif(!empty($data['excluded'])) {
      $arrayExcluded = $data['excluded'];
    }
    $toDeleteIds = array();
    $allCollection = $this->_objectManager->create('WIO\Forum\Model\Topic')->getCollection();
    $allCollection->notDeleted();
    
    foreach($allCollection as $item) {
      $toDeleteIds[] = $item->getId();
    }
    
    $arrayExcluded = is_array($arrayExcluded) ? $arrayExcluded : array();
    return array_diff($toDeleteIds, $arrayExcluded);  
  }
  
  protected function statusAllPosts($topic_id) {
    $modelFactory = $this->_objectManager->get('WIO\Forum\Model\PostFactory');
    $collection = $modelFactory->create()->getCollection();
    $collection->getSelect()->where('parent_id=? AND is_deleted=0', $topic_id);
    $count = 0;
    foreach($collection as $itemDel){
      $model = $modelFactory->create()->load($itemDel->getId());
      $model->setStatus(1);
      $model->save();
      $count++;
    }
    return $count;
  }
}
