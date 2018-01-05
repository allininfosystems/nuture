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

namespace WIO\Forum\Block\Forum;

class Index extends \Magento\Framework\View\Element\Template {

    protected $_forumModelFactory;
    protected $_forumModel;
    protected $_forumData;
    protected $_icon;
    protected $_registry;
    protected $_helperUrl;
    protected $_forumUser;
    protected $_latestModel;
    protected $_moderatorModel;
    protected $_isModerator;
    
    const CACHE_TAG = 'forum_index_index';

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context, 
        \WIO\Forum\Model\ResourceModel\Forum\CollectionFactory $forumModelFactory, 
        \WIO\Forum\Model\ForumFactory $forumModel, 
        \WIO\Forum\Helper\Data $forumData, 
        \WIO\Forum\Helper\Url $helperUrl, 
        \WIO\Forum\Model\Icon $icon, 
        \Magento\Framework\Registry $registry, 
        \WIO\Forum\Model\User $forumUser, 
        \WIO\Forum\Model\Latest $latestModel, 
        \WIO\Forum\Model\Moderator $moderatorModel, 
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_forumModelFactory = $forumModelFactory;
        $this->_forumData = $forumData;
        $this->_icon = $icon;
        $this->_registry = $registry;
        $this->_helperUrl = $helperUrl;
        $this->_forumUser = $forumUser;
        $this->_latestModel = $latestModel;
        $this->_forumModel = $forumModel;
        $this->_moderatorModel = $moderatorModel;
        $this->setIsmoderator();
    }

    protected function setIsmoderator() {
        if ($customer = $this->getCustomer()) {
            $this->_isModerator = $this->_moderatorModel->isModerator($customer->getId());
        }
    }

    protected function _prepareLayout() {
        $pageLayout = $this->_forumData->getForumPageLayout();
        if ($pageLayout) {
            $this->pageConfig->setPageLayout($pageLayout);
        }
        if($this->_forumData->getIsLayoutUpdated()) {
            return parent::_prepareLayout();
        }
        
        $defaultTitle = $this->_forumData->getForumDefaultTitle();
        if($defaultTitle) {
            $this->pageConfig->getTitle()->set($defaultTitle);
        }else{
            $this->pageConfig->getTitle()->set(__('Forum'));
        }
        
        
        $defaultMetaDesc = $this->_forumData->getForumDefaultDesc();
        if($defaultMetaDesc) {
            $this->pageConfig->setDescription($defaultMetaDesc);
        }
        $defaultMetaKeys = $this->_forumData->getForumDefaultKeys();
        if($defaultMetaKeys) {
            $this->pageConfig->setKeywords($defaultMetaKeys);
        }
        
        return parent::_prepareLayout();
    }

    public function getIsModerator() {
        return $this->_isModerator;
    }

    public function getTimeAccordingToTimeZone($dateTime) {
        return $this->_forumData->getTimeAccordingToTimeZone($dateTime);
    }

    public function getAllForums() {
        return $this->_forumModelFactory->create()
                        ->enabledOnly()
                        ->forumsOnly()
                        ->addStoreFilterToCollection()
                        ->setOrder($this->getSortField(), $this->getSortType())
                        ->setCurPage($this->getPageNum());
    }

    public function getSortType() {
        return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_SORT_KEY_REGISTER);
    }

    protected function getSortField() {
        return \WIO\Forum\Helper\Constant::WIO_FORUM_CREATED_TIME_SORT_FIELD;
    }

    protected function getPageNum() {
        return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_PAGE_KEY_REGISTER);
    }

    protected function getPageLimit() {
        return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_LIMIT_KEY_REGISTER);
    }

    protected function getCustomer() {
        return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION);
    }

    public function getIdentities(){
        return [self::CACHE_TAG . '_'];
    }
}
