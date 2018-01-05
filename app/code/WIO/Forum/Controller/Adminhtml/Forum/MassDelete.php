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

namespace WIO\Forum\Controller\Adminhtml\Forum;

class MassDelete extends \Magento\Backend\App\Action {

  /**
   * {@inheritdoc}
   */
  protected function _isAllowed() {
    return $this->_authorization->isAllowed('WIO_Forum:forum_delete');
  }

  public function execute() {
    $data = $this->getRequest()->getPostValue();
    $ids = $this->getIdsDelete($data);
    $resultRedirect = $this->resultRedirectFactory->create();
    $forumsDeleted = 0;
    $topicsDeleted = 0;
    $postsDeleted = 0;
    if (is_array($ids)) {
      try {
        foreach ($ids as $id) {
          $model = $this->_objectManager->create('WIO\Forum\Model\Forum');
          $model->load($id);
          if ($model->getId()) {
            $model->setIsDeleted(1);

            $topicsDeleted += $this->deleteAllTopics($model->getId());
            $postsDeleted += $this->deleteAllPosts($model->getId());
            $model->save();
          }
          $forumsDeleted++;
        }
        $this->messageManager->addSuccess(__('Total %1 forum(s) deleted', $forumsDeleted));
        $this->messageManager->addSuccess(__('Total %1 topic(s) deleted', $topicsDeleted));
        $this->messageManager->addSuccess(__('Total %1 post(s) deleted', $postsDeleted));
      } catch (Exception $ex) {
        $this->messageManager->addException($ex, __('Something went wrong while deleteing the forum.'));
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
    $allCollection = $this->_objectManager->create('WIO\Forum\Model\Forum')->getCollection();
    $allCollection->forumsOnly();
    
    foreach($allCollection as $item) {
      $toDeleteIds[] = $item->getId();
    }
    $arrayExcluded = is_array($arrayExcluded) ? $arrayExcluded : array();
    return array_diff($toDeleteIds, $arrayExcluded); 
  }
  
  protected function deleteAllTopics($parent_id){
    $modelFactory = $this->_objectManager->get('WIO\Forum\Model\TopicFactory');
    $collection = $modelFactory->create()->getCollection();
    $collection->getSelect()->where('parent_id=? AND is_deleted=0', $parent_id);
    $count = 0;
    foreach($collection as $itemDel){
      $model = $modelFactory->create()->load($itemDel->getId());
      $model->setIsDeleted(1);
      $model->save();
      $count++;
    }
    return $count;
  }
  
  protected function deleteAllPosts($forum_id) {
    $modelFactory = $this->_objectManager->get('WIO\Forum\Model\PostFactory');
    $collection = $modelFactory->create()->getCollection();
    $collection->getSelect()->where('forum_id=? AND is_deleted=0', $forum_id);
    $count = 0;
    foreach($collection as $itemDel){
      $model = $modelFactory->create()->load($itemDel->getId());
      $model->setIsDeleted(1);
      $model->save();
      $count++;
    }
    return $count;
  }
}
