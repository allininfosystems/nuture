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

use Magento\Backend\App\Action;
use WIO\Forum\Model\Url;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Save extends \Magento\Backend\App\Action {

  protected $dataProcessor;
  protected $modelUrl;
  protected $_date;

  public function __construct(Action\Context $context, 
          PostDataProcessor $dataProcessor, Url $forumUrl, DateTime $date) {
    $this->dataProcessor = $dataProcessor;
    $this->modelUrl = $forumUrl;
    $this->_date = $date;
    parent::__construct($context);
  }

  /**
   * {@inheritdoc}
   */
  protected function _isAllowed() {
    return $this->_authorization->isAllowed('WIO_Forum:topic_save');
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
      $model = $this->_objectManager->create('WIO\Forum\Model\Topic');


      $data['is_category'] = 0;

      $id = $this->getRequest()->getParam('topic_id');
      if ($id) {
        $model->load($id);
        $data['update_time'] = $this->_date->gmtDate();
      } else {
        $data['created_time'] = $this->_date->gmtDate();
      }

      /* // todo move to dataprocessor // */

      $urlKeyValid = true;
      if (empty($data['url_text'])) {
        $data['url_text'] = $this->modelUrl->buildUrlKeyFromTitle($data['title'], $id);
      } else {
        if ($this->modelUrl->notValidUrlKey($data['url_text'], $id)) {
          $errMessage = __('Invalid url text for topic "%1", its already exists', $data['url_text']);
          $urlKeyValid = false;
        }
      }

      $model->setData($data);
      $model->setSystemUserId(\WIO\Forum\Helper\Constant::WIO_FORUM_ADMIN_ID);
      
      if (!$this->dataProcessor->validate($data) || $urlKeyValid == false) {
        if ($errMessage) {
          $this->messageManager->addError($errMessage);
        }
        return $resultRedirect->setPath('*/*/edit', ['topic_id' => $model->getId(), '_current' => true]);
      }

      try {
        $model->save();
        $this->messageManager->addSuccess(__('You saved this topic.'));
        $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
        if ($this->getRequest()->getParam('back')) {
          return $resultRedirect->setPath('*/*/edit', ['topic_id' => $model->getId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
      } catch (\Magento\Framework\Exception\LocalizedException $e) {
        $this->messageManager->addError($e->getMessage());
      } catch (\RuntimeException $e) {
        $this->messageManager->addError($e->getMessage());
      } catch (\Exception $e) {
        var_dump($e->getMessage());
        die();
        $this->messageManager->addException($e, __('Something went wrong while saving the topic.'));
      }

      $this->_getSession()->setFormData($data);
      return $resultRedirect->setPath('*/*/edit', ['topic_id' => $this->getRequest()->getParam('topic_id')]);
    }
    return $resultRedirect->setPath('*/*/');
  }

}
