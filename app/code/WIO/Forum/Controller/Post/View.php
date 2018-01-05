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

namespace WIO\Forum\Controller\Post;

class View extends \Magento\Framework\App\Action\Action {
  
  protected $_viewReply;
  protected $_url;
  protected $_postId;

  public function __construct(
    \Magento\Framework\App\Action\Context $context,
    \WIO\Forum\Model\ViewReply $viewReply,
    \WIO\Forum\Helper\Url $url
  ) {
    $this->_viewReply = $viewReply;
    $this->_url = $url;
    parent::__construct($context);
  }
  
  public function dispatch(
    \Magento\Framework\App\RequestInterface $request
  ) {
    $this->_postId = $request->getParam(\WIO\Forum\Helper\Constant::WIO_FORUM_POST_ID_PARAM_NAME);
    if(!$this->_postId) {
      return $this->_redirect('');
    }
    return parent::dispatch($request);
  }
  
  public function execute() {
    $postArr = $this->_viewReply->getPostArray($this->_postId);
    if(empty($postArr) || empty($postArr['post']) || empty($postArr['url'])) {
      return $this->_redirect('');
    }
    return $this->_redirect($postArr['url']);
  }
}
