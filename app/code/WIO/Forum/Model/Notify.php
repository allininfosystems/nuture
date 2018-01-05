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

namespace WIO\Forum\Model;

class Notify {
  
  const NOTIFY_CUSTOMER_EMAIL_ID = 'wio_forum_notify_customer';

  protected $_notificationModel;
  protected $_helperUrl;
  protected $_customerModel;
  protected $_storeManager;
  protected $_transportBuilder;
  protected $_inlineTranslation;
  protected $_forumData;
  protected $_forumUser;

  public function __construct(
    \WIO\Forum\Helper\Url $helperUrl, 
    \Magento\Customer\Model\CustomerFactory $customerFactory, 
    \Magento\Store\Model\StoreManagerInterface $storeManager, 
    \WIO\Forum\Helper\Data $forumData, 
    \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,  
    \WIO\Forum\Model\User $forumUser,
    \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation, 
    NotificationFactory $notificationModel
  ) {
    $this->_forumData = $forumData;
    $this->_helperUrl = $helperUrl;
    $this->_notificationModel = $notificationModel;
    $this->_customerModel = $customerFactory;
    $this->_storeManager = $storeManager;
    $this->_transportBuilder = $transportBuilder;
    $this->_inlineTranslation = $inlineTranslation;
    $this->_forumUser = $forumUser;
  }

  public function remove($hash) {
    $notify = $this->_notificationModel->create()->load($hash, 'hash');
    if ($notify->getId()) {
      $notify->delete();
      return true;
    }
  }

  public function saveNew($topicId, $customerId) {

    $collectionNotification = $this->_notificationModel->create()->getCollection();
    $collectionNotification->loadByCustomerTopic($topicId, $customerId);

    if ($collectionNotification->getSize()) {
      return;
    }

    $notification = $this->_notificationModel->create();
    $notification->setTopicId($topicId);
    $notification->setSystemUserId($customerId);

    $notification->save();
  }

  public function sendNotifications($topic, $customerId, $post) {
    $notifyCollection = $this->_notificationModel->create()->getCollection();
    $notifyCollection->getNotifyCustomers($topic->getId(), $customerId);

    if ($notifyCollection->getSize()) {
      foreach ($notifyCollection as $notify) {
        $this->sendEmailNotify( $notify, $topic, $post, $customerId);
      }
    }
    
  }

  protected function sendEmailNotify($notify, $topic, $post, $customerId) {
    if(!$topic->getId() || !$notify->getId()) {
      return;
    }
    $viewPostUrl = $this->getViewPostUrl($post->getId());
    $unsubscribeUrl = $this->getUnsubscribeUrl($notify->getHash());
    $customer = $this->getCustomerModel($notify->getSystemUserId());
    $emailData = array(
      'url' => $viewPostUrl,
      'topic_name' => $topic->getTitle(),
      'now' => $this->_forumData->getNowTime(),
      'posted_by' => $this->_forumUser->getForumUserName($customerId),
      'unsubscribe_link' => $unsubscribeUrl
    );
    
    
    $this->sendEmailTemplate(
            $customer, 
            self::NOTIFY_CUSTOMER_EMAIL_ID, 
            $emailData
    );
    
  }

  private function sendEmailTemplate(
    $customer, 
    $templateId, 
    $templateParams = [],
    $email = null
  ) {
    $senderInfo = array(
      'name' => $this->_forumData->getSenderEmailName(),
      'email' => $this->_forumData->getSenderEmailAddress()
    );
    
    if ($email === null) {
      $email = $customer->getEmail();
    }

    $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
              [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND, 
                'store' => $this->_storeManager->getStore()->getId()
              ]
            )
            ->setTemplateVars($templateParams)
            ->setFrom($senderInfo)
            ->addTo($email, $this->_forumUser->getForumUserName($customer->getId()))
            ->getTransport();

    $transport->sendMessage();
  }
  
  protected function getCustomerModel($customer_id) {
    return $this->_customerModel->create()
            ->load($customer_id);
  }

  protected function getViewPostUrl($post_id) {
    return $this->_helperUrl->getLatestViewUrl($post_id);
  }

  protected function getUnsubscribeUrl($hash) {
    return $this->_helperUrl->getUnsubscribeUrl($hash);
  }

}
