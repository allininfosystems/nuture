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

namespace WIO\Forum\Controller\Adminhtml\Post;

/* // todo add data processor // */

class PostDataProcessor {

  public function filter($data) {
    $data['post'] = $this->cleanJS($data['post']);
    return $data;
  }

  public function validate($data) {
    /* // for data validation // */
    $errorNo = true;
    return $errorNo;
  }

  protected function cleanJS($postText) {
    $postText = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $postText);
    $postText = preg_replace('#(onevent|onclick|ondblclick|onmouseover|onmousedown|onmouseout|onhover)="[^"]+"#i', '', $postText);
    $postText = preg_replace('#javascript\:#i', '', $postText);
    return $postText;
  }

}
