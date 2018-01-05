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

namespace WIO\Forum\Controller;

use WIO\Forum\Helper\Data as forumData;

class Router implements \Magento\Framework\App\RouterInterface {

  protected $actionFactory;
  protected $_eventManager;
  protected $_storeManager;
  protected $_url;
  protected $_response;
  protected $_forumData;
  protected $_forumModel;
  protected $_topicModel;

  public function __construct(
    \Magento\Framework\App\ActionFactory $actionFactory, 
    \Magento\Framework\Event\ManagerInterface $eventManager, 
    \Magento\Framework\UrlInterface $url, 
    \Magento\Store\Model\StoreManagerInterface $storeManager, 
    \Magento\Framework\App\ResponseInterface $response, 
    \WIO\Forum\Model\ForumFactory $forumModelFactory,  
    \WIO\Forum\Model\TopicFactory $topicModelFactory, 
    forumData $forumData
  ) {
    $this->actionFactory = $actionFactory;
    $this->_eventManager = $eventManager;
    $this->_url = $url;
    $this->_storeManager = $storeManager;
    $this->_response = $response;
    $this->_forumData = $forumData;
    $this->_forumModel = $forumModelFactory->create();
    $this->_topicModel = $topicModelFactory->create();
  }

  public function match(\Magento\Framework\App\RequestInterface $request) {

    $forum_url_path = '';
    $topic_url_path = '';
    if (!$this->_forumData->getIsForumEnabled()) {
      return;
    }
    $identifier = trim($request->getPathInfo(), '/');
    if ($identifier == '' || !$identifier) {
      return;
    }
    $forumRoute = $this->_forumData->getForumRoute();

    $paths = explode('/', $identifier, 2);
    $currentRoute = trim($paths[0]);
    if ($currentRoute != $forumRoute) {
      return;
    }
    if (!empty($paths[1])) {
      $forum_url_path = $paths[1];
    }
    if ($forum_url_path == '') {
      $request = $this->setDefaultAction($request);
    } else {
      if(strstr($forum_url_path, '/')) {
        $paths_two = explode('/', $forum_url_path, 2);
        $forum_url_path = trim($paths_two[0]);
        $topic_url_path = trim($paths_two[1]);
      }
      
      $itemForum = $this->getItemByRoute($forum_url_path);
      if(!$itemForum){
        return;
      }
      if ($itemForum->getId() && !$topic_url_path) {
        
        $request = $this->setForumViewAction($request, $itemForum->getId());
        
      } elseif( $itemForum->getId() && $topic_url_path ){
        
        $itemTopic = $this->getItemTopicByRoute($topic_url_path); 
        
        if(!$itemTopic) {
          return null;
        }
        
        if ($itemTopic->getId()) {
          $request = $this->setTopicViewAction($request, $itemTopic->getId());
        }else{
          return null;
        }
        
      }else{
        return null;
      }
    }

    $request->setAlias(\Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
    return $this->actionFactory->create('Magento\Framework\App\Action\Forward');
  }
  
  protected function setTopicViewAction($request, $topic_id){
    $request
      ->setModuleName('wio_forum')
      ->setControllerName('topic')
      ->setActionName('index')
      ->setParam(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_ID_PARAM_NAME, $topic_id);
    return $request;
  }
  
  protected function setForumViewAction($request, $forum_id){
    $request
        ->setModuleName('wio_forum')
        ->setControllerName('forum')
        ->setActionName('view')
        ->setParam(\WIO\Forum\Helper\Constant::WIO_FORUM_ID_PARAM_NAME, $forum_id);
    return $request;
  }
  
  protected function setDefaultAction($request) {
    $request
        ->setModuleName('wio_forum')
        ->setControllerName('forum')
        ->setActionName('index');
    
    return $request;
  }

  protected function getItemByRoute($route) {
    $modelItem = $this->_forumModel->load($route, 'url_text');
    if ($modelItem->getIsDeleted() == 0) {
      return $modelItem;
    }
  }

  protected function getItemTopicByRoute($route) {
    $modelItem = $this->_topicModel->load($route, 'url_text');
    if ($modelItem->getIsDeleted() == 0) {
      return $modelItem;
    }
  }

}
