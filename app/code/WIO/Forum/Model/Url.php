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

namespace WIO\Forum\Model;

use WIO\Forum\Model\Forum;

class Url extends \Magento\Framework\Model\AbstractModel {

  const URL_CHECK_ADDON = 'simple-forum';
  const REG_EXP_FORUM_REPLACE = "/[^a-zA-Z0-9-\.\-\s]/";

  protected $_objectForum;

  public function __construct(\Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, Forum $objectForum) {
    $this->_objectForum = $objectForum;
    parent::__construct($context, $registry);
  }

  public function buildUrlKeyFromTitle($title, $id) {
    $title_to_check = $new_title = $this->getUrlKeyFromTitle(str_replace(' ', '-', strtolower($title)));
    $t = 1;
    if (str_replace('.', '', str_replace('-', '', $title_to_check)) == '') {
      $addon = date('Y-m-d');
      $new_title = $title_to_check = $this->getUrlTitleAddon() . '-' . $addon;
    }
    while ($this->notValidUrlKey($title_to_check, $id)) {
      $t++;
      $title_to_check = $new_title . ($t ? '-' . $t : '');
    }
    return $title_to_check;
  }

  public function notValidUrlKey($key, $id, $store_id = false) {
    $collection = $this->getModelByUrlKey($key, $id, $store_id);
    return (($collection->getSize()) > 0 ? true : false);
  }

  protected function getModelByUrlKey($key, $not_id = false, $store_id = false) {

    //$model = $this->_objectManager->create('WIO\Forum\Model\Forum');
    $collection = $this->_objectForum->getCollection();
    $collection->getSelect()->where('url_text=?', trim($key))
            ->where('is_deleted=?', '0');
    if ($not_id) {
      $collection->getSelect()->where('topic_id!=?', $not_id);
    }
    if ($store_id) {
      $collection->getSelect()->where('store_id=? OR store_id = 0', $store_id);
    }
    return $collection;
  }

  protected function getUrlKeyFromTitle($string) {
    $r = preg_replace($this->getRexExpReplace(), "", $string);
    return $r;
  }

  protected function getRexExpReplace() {
    return self::REG_EXP_FORUM_REPLACE;
  }

  protected function getUrlTitleAddon() {
    return self::URL_CHECK_ADDON;
  }

}
