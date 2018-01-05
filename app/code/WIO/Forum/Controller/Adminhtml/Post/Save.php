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

use Magento\Backend\App\Action;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Save extends \Magento\Backend\App\Action {
  
  protected $dataProcessor;
  protected $_date;

  public function __construct(
          Action\Context $context, 
          PostDataProcessor $dataProcessor, 
          DateTime $date
  ) {
    $this->dataProcessor = $dataProcessor;
    $this->_date = $date;
    parent::__construct($context);
  }
  
  /**
   * {@inheritdoc}
   */
  protected function _isAllowed() {
    return $this->_authorization->isAllowed('WIO_Forum:forum_post_save');
  }
  
  /**
   * Save action
   *
   * @return \Magento\Framework\Controller\ResultInterface
   */
  public function execute() {
    $data = $this->getRequest()->getPostValue();

    /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
    $resultRedirect = $this->resultRedirectFactory->create();
  
    if ($data) {
      $data = $this->dataProcessor->filter($data);
      if(trim($data['post']) == ''){
        return $resultRedirect->setPath('*/*/');
      }
      $model = $this->_objectManager->create('WIO\Forum\Model\Post');

      $id = $this->getRequest()->getParam('post_id');
      if ($id) {
        $model->load($id);
        $model->setUpdateTime($this->_date->gmtDate());
      } else {
        $model->setCreatedTime($this->_date->gmtDate());
      }
      
      $model->setPost($data['post']);
      $model->setPostOrig(strip_tags($data['post']));
      $model->setStatus($data['status']);
      $model->setParentId($data['parent_id']);
      $model->setSystemUserId(\WIO\Forum\Helper\Constant::WIO_FORUM_ADMIN_ID);
      
      $this->messageManager->getMessages(true);
      try {
        $model->save();
        $this->messageManager->addSuccess(__('You saved this post.'));
        $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
        if ($this->getRequest()->getParam('back')) {
          return $resultRedirect->setPath('*/*/edit', ['post_id' => $model->getId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
      } catch (\Magento\Framework\Exception\LocalizedException $e) {
        $this->messageManager->addError($e->getMessage());
      } catch (\RuntimeException $e) {
        $this->messageManager->addError($e->getMessage());
      } catch (\Exception $e) {
         $this->messageManager->addException($e, __('Something went wrong while saving the post.'));
      }
      $this->_getSession()->setFormData($data);
      return $resultRedirect->setPath('*/*/edit', ['post_id' => $this->getRequest()->getParam('topic_id')]);
    }
    return $resultRedirect->setPath('*/*/');
  }
}
