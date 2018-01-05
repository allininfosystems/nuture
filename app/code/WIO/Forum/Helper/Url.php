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

class Url extends \Magento\Framework\App\Helper\AbstractHelper {
  
  protected $_storeManagerr;
  protected $_forumData;
    
  public function __construct(
    \Magento\Framework\App\Helper\Context $context,
    \Magento\Store\Model\StoreManagerInterface $storeManager,
    \WIO\Forum\Helper\Data $forumData 
  ) {
    $this->_storeManager = $storeManager;
    $this->_forumData = $forumData;
    parent::__construct($context);
  }
  
  public function getBaseForumUrl(){
    return $this->_forumData->getForumRoute();
  }
  
  public function getForumUrl($forumObj = NULL, $params = array()) {
    return $this->_storeManager->getStore()->getBaseUrl() . 
            '' . $this->_forumData->getForumRoute()
            . '/' . ($forumObj != NULL ? $forumObj->getUrlText() : '')
      . $this->prepareParams($params);
  }
  
  public function getTopicUrl($forumObj, $topicObj, $params = array()){
    return $this->_storeManager->getStore()->getBaseUrl() . 
            '' . $this->_forumData->getForumRoute()
            . '/' . ( $forumObj->getUrlText()) . '/' . $topicObj->getUrlText()
      . $this->prepareParams($params);
  }
  
  public function getAddTopicUrl($forumObj, $topicObj = null, $postObj = null) {
    $urlStr = Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/topic/' . 
            ($topicObj ? 'edit' : 'new');
    $params = array(
        Constant::WIO_FORUM_ID_PARAM_NAME => $forumObj->getId()
    );
    if($topicObj !== null) {
      $params[Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME] = $topicObj->getId();
    }
    if($postObj !== null) {
      $params[Constant::WIO_FORUM_POST_ID_PARAM_NAME] = $postObj->getId();
    }
    return $this->_getUrl($urlStr, $params);
  }
  
  public function getLatestViewUrl($post_id){
    $urlStr = Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/post/' . 
            'view';
    $params = array(
        Constant::WIO_FORUM_POST_ID_PARAM_NAME => $post_id
    );
    return $this->_getUrl($urlStr, $params);
  }
  
  public function getDeletePostUrl($post_id){
    $urlStr = Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/post/' . 
            'delete';
    $params = array(
        Constant::WIO_FORUM_POST_ID_PARAM_NAME => $post_id
    );
    return $this->_getUrl($urlStr, $params);
  }
  
  public function getDeleteTopicLink($topic_id){
    $urlStr = Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/topic/' . 
            'delete';
    $params = array(
        Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME => $topic_id
    );
    return $this->_getUrl($urlStr, $params);
  }
  
  public function getEditTopicUrl($forum_id, $topic_id, $post_id = null) {
    $urlStr = Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/topic/' . 
            'edit';
    $params = array(
        Constant::WIO_FORUM_ID_PARAM_NAME => $forum_id,
        Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME => $topic_id,
        Constant::WIO_FORUM_POST_ID_PARAM_NAME => $post_id
    );
    return $this->_getUrl($urlStr, $params);
  }
  
  public function getUnsubscribeUrl($hash) {
    $urlStr = Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/notify/' . 
            'remove';
    $params = array(
        'hash' => $hash
    );
    return $this->_getUrl($urlStr, $params);
  }
  
  protected function prepareParams($params = array()){
    if(count($params) == 0){
      return '';
    }
    return '?' . http_build_query($params);
  }
}