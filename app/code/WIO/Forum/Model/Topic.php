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

use Magento\Framework\DataObject\IdentityInterface;

class Topic extends \Magento\Framework\Model\AbstractModel implements IdentityInterface {

  const STATUS_ENABLED = 1;
  const STATUS_DISABLED = 0;
  const CACHE_TAG = 'topic_block';

  /**
   * @var string
   */
  protected $_cacheTag = 'topic_block';
  
  const IDENTIFIER = 'topic_model_';
  /**
   * @return void
   */
  protected function _construct() {
    $this->_init('WIO\Forum\Model\ResourceModel\Topic');
  }

  /**
   * Get identities
   *
   * @return array
   */
  public function getIdentities() {
    return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getIdentifier()];
  }

  /**
   * Retrieve block identifier
   *
   * @return string
   */
  public function getIdentifier() {
    return (string) $this->getData(self::IDENTIFIER);
  }

  public function getAvailableStatuses() {
    return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
  }

}
