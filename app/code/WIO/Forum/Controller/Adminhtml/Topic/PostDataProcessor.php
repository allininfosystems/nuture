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

namespace WIO\Forum\Controller\Adminhtml\Topic;

/* // todo add data processor // */

class PostDataProcessor {

  public function __construct() {
    
  }

  public function filter($data) {
    if (!empty($data['icon_id']) && !empty($data['icon_id'][0])) {
      $data['icon_id'] = $data['icon_id'][0];
    } else {
      $data['icon_id'] = '';
    }
    return $data;
  }

  public function validate($data) {
    /* // for data validation // */
    $errorNo = true;
    return $errorNo;
  }

}
