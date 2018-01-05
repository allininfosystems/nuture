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

class Params extends \Magento\Framework\App\Helper\AbstractHelper {

  protected $_session;
  protected $_allowed_sorting = array(
      'asc',
      'desc'
  );

  public function __construct(
  \Magento\Framework\App\Helper\Context $context, \WIO\Forum\Model\Session $session
  ) {
    parent::__construct($context);
    $this->_session = $session;
  }
  
  public function getPageLimit($request, $key, $default_value = 5) {
    if ($request->getParam(Constant::WIO_FORUM_PER_PAGE)) {
      $this->_session->setData($key, $request->getParam(Constant::WIO_FORUM_PER_PAGE));
      return $request->getParam(Constant::WIO_FORUM_PER_PAGE);
    }
    if ($this->_session->getData($key)) {
      return $this->_session->getData($key);
    } else {
      return $default_value;
    }
  }

  public function getSortType($request, $key,  $default_value = 'asc') {
    if ($request->getParam(Constant::WIO_FORUM_SORTING) &&
            in_array($request->getParam(Constant::WIO_FORUM_SORTING), $this->_allowed_sorting)) {
      $this->_session->setData($key, $request->getParam(Constant::WIO_FORUM_SORTING));
      return $request->getParam(Constant::WIO_FORUM_SORTING);
    }
    if ($this->_session->getData($key)) {
      return $this->_session->getData($key);
    } else {
      return $default_value;
    }
  }

  public function getPageNumber($request, $key, $default_value = 1) {
    if ($request->getParam(Constant::WIO_FORUM_PAGE_NUM)) {
      $this->_session->setData($key, $request->getParam(Constant::WIO_FORUM_PAGE_NUM));
      return $request->getParam(Constant::WIO_FORUM_PAGE_NUM);
    }
    if ($this->_session->getData($key)) {
      return $this->_session->getData($key);
    } else {
      return $default_value;
    }
  }

}
