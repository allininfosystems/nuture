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

namespace WIO\Forum\Block\Topic;

class Index extends \Magento\Framework\View\Element\Template {

    protected $_forumData;
    protected $_icon;
    protected $_registry;
    protected $_helperUrl;
    protected $_postsModel;
    protected $_forumUser;
    protected $_moderatorModel;
    protected $_isModerator;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \WIO\Forum\Helper\Data $forumData, \WIO\Forum\Helper\Url $helperUrl, \WIO\Forum\Model\Icon $icon, \WIO\Forum\Model\PostFactory $postsModel, \Magento\Framework\Registry $registry, \WIO\Forum\Model\User $forumUser, \WIO\Forum\Model\Moderator $moderatorModel, array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_forumData = $forumData;
        $this->_icon = $icon;
        $this->_registry = $registry;
        $this->_helperUrl = $helperUrl;
        $this->_postsModel = $postsModel;
        $this->_forumUser = $forumUser;
        $this->_moderatorModel = $moderatorModel;
        $this->setIsmoderator();
    }

    public function getAllPosts() {
        return $this->_postsModel->create()->getCollection()
                        ->byParent($this->getTopicId())
                        ->enabledOnly()
                        ->notDeleted()
                        ->setOrder($this->getSortField(), $this->getSortType())
                        ->setCurPage($this->getPageNum());
    }

    protected function _prepareLayout() {
        $keywordsSet = false;
        $descSet = false;

        $this->pageConfig->getTitle()->set(__('%1 - %2', $this->getParentForum()->getTitle(), $this->getParentTopic()->getTitle()));
        if ($this->getParentForum() && $this->getParentTopic()->getMetaDescription()) {
            $this->pageConfig->setDescription($this->getParentTopic()->getMetaDescription()); //->set($this->getParentForum()->getMetaDescription());

            $descSet = true;
        }
        if ($this->getParentForum() && $this->getParentTopic()->getMetaKeywords()) {
            $this->pageConfig->setKeywords($this->getParentTopic()->getMetaKeywords()); //->set($this->getParentForum()->getMetaDescription());

            $keywordsSet = true;
        }
        if (!$descSet && $this->getParentForum() && $this->getParentForum()->getMetaDescription()) {
            $descSet = true;
            $this->pageConfig->setDescription($this->getParentForum()->getMetaDescription()); //->set($this->getParentForum()->getMetaDescription());
        }
        if (!$keywordsSet && $this->getParentForum() && $this->getParentForum()->getMetaKeywords()) {
            $keywordsSet = true;
            $this->pageConfig->setKeywords($this->getParentForum()->getMetaKeywords()); //->set($this->getParentForum()->getMetaDescription());
        }

        $defaultMetaDesc = $this->_forumData->getForumDefaultDesc();
        if ($defaultMetaDesc && !$descSet) {
            $this->pageConfig->setDescription($defaultMetaDesc);
        }
        $defaultMetaKeys = $this->_forumData->getForumDefaultKeys();
        if ($defaultMetaKeys && !$keywordsSet) {
            $this->pageConfig->setKeywords($defaultMetaKeys);
        }
        
        $this->_forumData->setLayoutUpdated();
        return parent::_prepareLayout();
    }

    public function getSortType() {
        return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_SORT_KEY_REGISTER);
    }

    protected function setIsmoderator() {
        if ($customer = $this->getCustomerSession()) {
            $this->_isModerator = $this->_moderatorModel->isModerator($customer->getId());
        }
    }

    public function getIsModerator() {
        return $this->_isModerator;
    }

    protected function getSortField() {
        return \WIO\Forum\Helper\Constant::WIO_FORUM_CREATED_TIME_SORT_FIELD;
    }

    protected function getPageNum() {
        return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_PAGE_KEY_REGISTER);
    }

    protected function getPageLimit() {
        return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_LIMIT_KEY_REGISTER);
    }

    protected function getParentForum() {
        return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_FORUM);
    }

    protected function getParentTopic() {
        return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PARENT_TOPIC);
    }

    public function getTopicId() {
        return $this->getParentTopic()->getId();
    }

    public function getForumId() {
        return $this->getParentForum()->getId();
    }

    public function getIsLoggedIn() {
        $customerSession = $this->getCustomerSession();
        return $customerSession->isLoggedIn();
    }

    public function getTimeAccordingToTimeZone($dateTime) {
        return $this->_forumData->getTimeAccordingToTimeZone($dateTime);
    }

    public function getCustomerSession() {
        return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION);
    }

    public function getIsOwner($object) {
        if ($this->getIsModerator()) {
            return true;
        }
        $customerSession = $this->getCustomerSession();
        if ($object->getSystemUserId() == $customerSession->getId()) {
            return true;
        }
    }

}
