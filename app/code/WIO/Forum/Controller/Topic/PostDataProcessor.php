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

namespace WIO\Forum\Controller\Topic;

class PostDataProcessor {
  
  public function __construct() {
    
  }
  
  public function filter($data) {
    $data['post']  = $this->cleanJS($data['post']);
    if(!empty($data['title'])) {
      $data['title'] = strip_tags($data['title']);
    }
    if(!empty($data['description'])) {
      $data['description'] = strip_tags($data['description']);
    }
    $data['title'] = (empty($data['title']) ? '-TITLE-NOT-ADDED' : $data['title']);
    return $data;
  }
  
  protected function cleanJS($postText) {
    $postText = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $postText);
    $postText = preg_replace('#(onevent|onclick|ondblclick|onmouseover|onmousedown|onmouseout|onhover)="[^"]+"#i', '', $postText);
    $postText = preg_replace('#javascript\:#i', '', $postText);
    return $postText;
  }
}