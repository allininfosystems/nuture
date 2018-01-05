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

namespace WIO\Forum\Block\Ui;

class FormSearch extends \Magento\Framework\View\Element\Template {

  protected $_forumData;
  protected $_registry;
  protected $_forumSession;

  public function __construct(
  \Magento\Framework\View\Element\Template\Context $context, 
  \WIO\Forum\Helper\Data $forumData, 
  \Magento\Framework\Registry $registry, 
  \WIO\Forum\Model\Session $forumSession,        
  array $data = []
  ) {
    parent::__construct($context, $data);
    $this->_registry = $registry;
    $this->_forumSession = $forumSession;
    $this->_forumData = $forumData;
  }

  protected function _prepareLayout() {
    parent::_prepareLayout();
    return $this;
  }

  protected function getRoute() {
    return $this->_forumData->getForumRoute();
  }

  public function getIsEnabled() {
    return $this->_forumData->getIsSearchEnabled();
  }

  public function getSearchAction() {
    return $this->getUrl(\WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/search/index');
  }

  public function getResetAction() {
    return $this->getUrl(\WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/search/reset');
  }

  public function getNameSearch(){
    return \WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_PARAMNAME;
  }
  
  public function getSearchPhrase() {
    return $this->_forumSession->getData(\WIO\Forum\Helper\Constant::WIO_FORUM_SEARCH_REGISTRATED);
  }
}

