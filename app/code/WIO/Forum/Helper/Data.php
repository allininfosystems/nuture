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

namespace WIO\Forum\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {
  protected $_dates;

  const FORUM_FRONT_ENABLED = 'wio_forum/general/enable';
  const FORUM_FRONT_ROUTE = 'wio_forum/general/forum_route';
  const FORUM_FRONTEND_TITLE = 'wio_forum/frontend/forum_title';
  const FORUM_FRONTEND_SEARCHENABLED = 'wio_forum/frontend/forum_search';
  const FORUM_FRONTEND_BOOKMARKSENABLED = 'wio_forum/frontend/bookmarks';
  const FORUM_FRONTEND_JUMPTOENABLED = 'wio_forum/frontend/jumpto';
  const FORUM_FRONTEND_RSSENABLED = 'wio_forum/frontend/rss';
  const FORUM_FRONTEND_ICONSENABLED = 'wio_forum/frontend/icons';
  const FORUM_FRONTEND_STATISTIC = 'wio_forum/frontend/statistic';
  const FORUM_FRONTEND_WHOISONLINE = 'wio_forum/frontend/whoisonline';
  const FORUM_FRONTEND_ALLOW_CONTROLS = 'wio_forum/frontend/allowedcontrols';
  const FORUM_FRONTEND_ALLOW_CUSTOMER_DELETE_TOPICS = 'wio_forum/frontend/allowdeletetopic';
  const FORUM_FRONTEND_PAGE_LAYOUT = 'wio_forum/frontend/page_layout';
  
  /* meta data defult */
  const FORUM_META_DEFUALT_TITLE = 'wio_forum/meta_data/title';
  const FORUM_META_DEFUALT_META_DESC = 'wio_forum/meta_data/meta_description';
  const FORUM_META_DEFUALT_META_KEYS = 'wio_forum/meta_data/meta_keywords';
  
  
  const FORUM_FRONTEND_ALLOW_CUSTOMER_NOTIFICATION = 'wio_forum/notification/customernotification';
  const FORUM_EMAIL_SENDER_ADDRESS = 'wio_forum/notification/senderemail';
  const FORUM_EMAIL_SENDER_NAME = 'wio_forum/notification/sendername';
  const DEFAULT_FORUM_ROUTE = 'forum';
  
  protected $_layoutForumUpdated = false;
  
  public function __construct(
    \Magento\Framework\App\Helper\Context $context, 
    \WIO\Forum\Helper\Dates $dates
  ) {
    parent::__construct($context);
    $this->_dates = $dates;
  }

  public function getIsLayoutUpdated() {
      return $this->_layoutForumUpdated;
  }
  
  public function setLayoutUpdated() {
      $this->_layoutForumUpdated = true;
  }
  public function getIsForumEnabled() {
    return $this->scopeConfig->getValue(self::FORUM_FRONT_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getForumRoute() {
    return ( $this->scopeConfig->getValue(self::FORUM_FRONT_ROUTE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) ? $this->scopeConfig->getValue(self::FORUM_FRONT_ROUTE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE) : self::DEFAULT_FORUM_ROUTE);
  }
  
  public function getForumDefaultTitle() {
    return $this->scopeConfig->getValue(self::FORUM_META_DEFUALT_TITLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }
  
  public function getForumDefaultDesc() {
    return $this->scopeConfig->getValue(self::FORUM_META_DEFUALT_META_DESC, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }
  
  public function getForumDefaultKeys() {
    return $this->scopeConfig->getValue(self::FORUM_META_DEFUALT_META_KEYS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getForumPageLayout() {
    return $this->scopeConfig->getValue(self::FORUM_FRONTEND_PAGE_LAYOUT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getForumTitleFront() {
    return $this->scopeConfig->getValue(self::FORUM_FRONTEND_TITLE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getIsRssEnabled() {
    return $this->scopeConfig->getValue(self::FORUM_FRONTEND_RSSENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getIsSearchEnabled() {
    return $this->scopeConfig->getValue(self::FORUM_FRONTEND_SEARCHENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getIsBookmarksEnabled() {
    return $this->scopeConfig->getValue(self::FORUM_FRONTEND_BOOKMARKSENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getIsJumpToEnabled() {
    return $this->scopeConfig->getValue(self::FORUM_FRONTEND_JUMPTOENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }
  
  public function getIsIconsEnabled() {
    return $this->scopeConfig->getValue(self::FORUM_FRONTEND_ICONSENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }
  
  public function getIsStatisticEnabled() {
    return $this->scopeConfig->getValue(self::FORUM_FRONTEND_STATISTIC, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }
  
  public function getIsWhoisonlineEnabled() {
    return $this->scopeConfig->getValue(self::FORUM_FRONTEND_WHOISONLINE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }
  
  public function getIsCustomerAllowedDeleteTopics() {
    return $this->scopeConfig->getValue(self::FORUM_FRONTEND_ALLOW_CUSTOMER_DELETE_TOPICS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }
  
  public function getIsAllowedControls() {
    return $this->scopeConfig->getValue(self::FORUM_FRONTEND_ALLOW_CONTROLS, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }

  public function getIsCustomerNotificationEnabled() {
    return $this->scopeConfig->getValue(self::FORUM_FRONTEND_ALLOW_CUSTOMER_NOTIFICATION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
  }
  
  public function getSenderEmailAddress() {
    return $this->scopeConfig->getValue(self::FORUM_EMAIL_SENDER_ADDRESS);
  }
  
  public function getSenderEmailName() {
    return $this->scopeConfig->getValue(self::FORUM_EMAIL_SENDER_NAME);
  }
  
  public function getTimeAccordingToTimeZone($dateTime) {
    return $this->_dates->getTimeAccordingToTimeZone($dateTime);
  }
  
  public function getNowTime(){
    $dateTime = date('Y-m-d H:i:s');
    return $this->_dates->getTimeAccordingToTimeZone($dateTime);
  }
}
