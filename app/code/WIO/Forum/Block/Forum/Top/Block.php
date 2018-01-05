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

namespace WIO\Forum\Block\Forum\Top;

use Magento\Framework\View\Element\Template;
use WIO\Forum\Helper\Data as forumData;

class Block extends \Magento\Framework\View\Element\Template {

  protected $_forumData;
  protected $_moderatorModel;
  protected $_registry;
  
  public function __construct(
  Template\Context $context, 
  forumData $forumData, 
  \WIO\Forum\Model\Moderator $moderatorModel, 
  \Magento\Framework\Registry $registry, 
  array $data = []
  ) {
    parent::__construct($context, $data);
    $this->_forumData = $forumData;
    $this->_moderatorModel = $moderatorModel;
    $this->_registry = $registry;
  }

  protected function _prepareLayout() {
    return parent::_prepareLayout();
  }

  public function getIsModerator() {
    if($customer = $this->getCustomer()) {
      return $this->_moderatorModel->isModerator($customer->getId());
    }
  }
  
  public function getCustomer() {
    return $this->_registry->registry(\WIO\Forum\Helper\Constant::WIO_FORUM_CUSTOMER_MODEL_SESSION);
  }
  
  public function getForumTitle() {
    return $this->_forumData->getForumTitleFront();
  }
  
  public function getIsAllowedControls(){
    return $this->_forumData->getIsAllowedControls();
  }

  public function getMyAccLink(){
    return $this->getUrl(\WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/customer');
  }
  
  public function getMyForumPostsLink(){
    return $this->getUrl(\WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/customer/posts');
  }
  
  public function getMyForumTopicsLink(){
    return $this->getUrl(\WIO\Forum\Helper\Constant::WIO_FORUM_FRONTEND_ROUTE_NAME . '/customer/topics');
  }
}
