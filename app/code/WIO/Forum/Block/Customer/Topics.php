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

namespace WIO\Forum\Block\Customer;

class Topics extends \Magento\Framework\View\Element\Template {

  protected $_forumData;
  protected $_registry;
  protected $_helperUrl;
  protected $_forumModel;
  protected $_topicModel;
  protected $_postModel;
  protected $_collection;
  protected $_forumsLoaded;
  protected $_moderatorModel;

  public function __construct(
  \Magento\Framework\View\Element\Template\Context $context, \WIO\Forum\Helper\Data $forumData, \WIO\Forum\Helper\Url $helperUrl, \WIO\Forum\Model\ForumFactory $forumModel, \WIO\Forum\Model\TopicFactory $topicModel, \WIO\Forum\Model\PostFactory $postModel, \WIO\Forum\Model\Moderator $moderatorModel, \Magento\Framework\Registry $registry, array $data = array()
  ) {
    $this->_forumData = $forumData;
    $this->_registry = $registry;
    $this->_helperUrl = $helperUrl;
    $this->_forumModel = $forumModel;
    $this->_topicModel = $topicModel;
    $this->_postModel = $postModel;
    $this->_moderatorModel = $moderatorModel;

    parent::__construct($context, $data);
  }

  protected function _prepareLayout() {
    parent::_prepareLayout();
    $this->initCollection();
    if (!$this->_collection) {
      return;
    }
    $pager = $this->getLayout()->createBlock(
            'Magento\Theme\Block\Html\Pager', 'wioforum.forum.pager'
    );
    $pager->setLimit($this->getPageLimit())
            ->setShowAmounts(true)
            ->setCollection($this->_collection);
    $pager->setAvailableLimit(array(10 => 10, 20 => 20, 30 => 30, 50 => 50));
    $this->setChild('pager', $pager);
  }

  protected function getCustomer() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION);
  }

  public function getIsModerator() {
    if ($customer = $this->getCustomer()) {
      return $this->_moderatorModel->isModerator($customer->getId());
    }
  }

  public function getPagerHtml() {
    return $this->getChildHtml('pager');
  }

  public function getForumUrl() {
    return $this->_helperUrl->getForumUrl();
  }

  public function getCollection() {
    return $this->_collection;
  }

  protected function initCollection() {
    $this->_collection = $this->_topicModel->create()->getCollection();
    $customer = $this->getCustomer();
    if (!$customer) {
      return;
    }
    $this->_collection->setCurPage($this->getPageNum());
    if(!$this->getIsModerator()) {
      $this->_collection->getUserTopics($customer->getId());
    }else{
      $this->_collection->enabledOnly()
              ->notDeleted()
              ->topicsOnly();
    }

    $this->_collection->setOrder($this->getSortField(), $this->getSortType());
  }

  public function getTimeAccordingToTimeZone($dateTime) {
    return $this->_forumData->getTimeAccordingToTimeZone($dateTime);
  }

  public function getSortType() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_SORT_KEY_REGISTER);
  }

  public function getParentForum($topic) {
    $parentId = $topic->getParentId();
    if (!empty($this->_forumsLoaded[$parentId])) {
      return $this->_forumsLoaded[$parentId];
    }
    $this->_forumsLoaded[$parentId] = $this->_forumModel->create()->load($parentId);
    return $this->_forumsLoaded[$parentId];
  }

  public function getParentForumUrl($parentForum) {
    return $this->_helperUrl->getForumUrl($parentForum);
  }

  public function getTopicUrl($parentForum, $_topic) {
    return $this->_helperUrl->getForumUrl($parentForum, $_topic);
  }

  public function getDeleteLink($_topic) {
    return $this->_helperUrl->getDeleteTopicLink($_topic->getId());
  }

  public function getEditLink($_topic) {
    $parentForum = $this->getParentForum($_topic);
    $firstPost = $this->getMyFirstPostId($_topic);
    return $this->_helperUrl->getEditTopicUrl($parentForum->getId(), $_topic->getId(), $firstPost);
  }

  public function getViewLink($_topic) {
    $parentForum = $this->getParentForum($_topic);
    return $this->_helperUrl->getTopicUrl($parentForum, $_topic);
  }

  public function getIsCustomerAllowedDeleteTopics() {
    return $this->_forumData->getIsCustomerAllowedDeleteTopics();
  }

  protected function getSortField() {
    return \WIO\Forum\Helper\Constant::WIO_FORUM_CREATED_TIME_SORT_FIELD;
  }

  protected function getPageNum() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_PAGE_KEY_REGISTER);
  }

  protected function getPageLimit() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_TOPIC_LIMIT_KEY_REGISTER);
  }

  protected function getMyFirstPostId($topicModel) {
    $customer = $this->getCustomer();
    if (!$customer) {
      return;
    }
    $postsCollection = $this->_postModel->create()->getCollection();
    $postsCollection->getUserPosts($customer->getId())
            ->byParent($topicModel->getId())
            ->setOrder($this->getSortField(), 'asc');
    if ($postsCollection->getSize()) {
      $itemFirst = $postsCollection->getFirstItem();
      return $itemFirst->getId();
    }
  }

  public function getSortUrl($type = 'asc') {
    return $this->getUrl('*/*/*', array(
                \WIO\Forum\Helper\Constant::WIO_FORUM_SORTING => $type
    ));
  }

}
