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

class Icon extends \Magento\Framework\Model\AbstractModel {

  const ADMINHTML_ICON_PATH = 'wioforum/icons/';
  const FRONTEND_ICON_PATH_SMALL = 'images/icons';
  const FRONTEND_ICON_PATH_BIG = 'images/icons/big';

  protected $forum_icons_id = array(
      'smile',
      'wink',
      'kiss',
      'saint',
      'evil',
      'question',
      'cry',
      'devil',
      'shame',
      'disappointed',
      'smart',
      'laughter',
      'monkey',
      'sad',
      'accept',
      'cancel',
  );
  protected $forum_icons_img = array(
      'accept' => 'accept.png',
      'cancel' => 'cancel.png',
      'saint' => 'saint.png',
      'evil' => 'evil.png',
      'question' => 'question.png',
      'cry' => 'cry.png',
      'devil' => 'devil.png',
      'shame' => 'shame.png',
      'disappointed' => 'disappointed.png',
      'smart' => 'smart.png',
      'laughter' => 'laughter.png',
      'smile' => 'smile.png',
      'monkey' => 'monkey.png',
      'sad' => 'sad.png',
      'kiss' => 'kiss.png',
      'wink' => 'wink.png',
  );
  protected $forum_icons_key_lang = array(
      'accept' => 'Accept',
      'cancel' => 'Cancel',
      'saint' => 'Saint',
      'evil' => 'Evil',
      'question' => 'Question',
      'cry' => 'Cry',
      'devil' => 'Devil',
      'shame' => 'Shame',
      'disappointed' => 'Disappointed',
      'smart' => 'Smart',
      'laughter' => 'Laughter',
      'smile' => 'Smile',
      'monkey' => 'Monkey',
      'sad' => 'Sad',
      'kiss' => 'Kiss',
      'wink' => 'Wink'
  );

  protected function getAllIconsIds() {
    return $this->forum_icons_id;
  }

  protected function getAllIconsLangs() {
    return $this->forum_icons_key_lang;
  }

  protected function getAllIconsImages() {
    return $this->forum_icons_img;
  }

  public function getAdminhtmlIconPath() {
    return self::ADMINHTML_ICON_PATH;
  }

  public function getFrontIconPathSmall() {
    return self::FRONTEND_ICON_PATH_SMALL;
  }

  public function getFrontIconPathBig() {
    return self::FRONTEND_ICON_PATH_BIG;
  }

  public function getForumIcons() {
    $icons = array();
    $icons['ids'] = $this->getAllIconsIds();
    $icons['img'] = $this->getAllIconsImages();
    $icons['key'] = $this->getAllIconsLangs();

    return $icons;
  }

  public function getIconFrontImgSmall($icon_id = NULL) {
    if ($icon_id !== NULL && $this->forum_icons_img[$icon_id]) {
      return $this->getFrontIconPathSmall($icon_id) . DIRECTORY_SEPARATOR . $this->forum_icons_img[$icon_id];
    }
  }

  public function getIconFrontImgBig($icon_id = NULL) {
    if ($icon_id !== NULL && $this->forum_icons_img[$icon_id]) {
      return $this->getFrontIconPathBig($icon_id) . DIRECTORY_SEPARATOR . $this->forum_icons_img[$icon_id];
    }
  }

}
