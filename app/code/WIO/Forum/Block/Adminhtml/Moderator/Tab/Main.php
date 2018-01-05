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

namespace WIO\Forum\Block\Adminhtml\Moderator\Tab;

class Main extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface {

  /**
   * Prepare label for tab
   *
   * @return \Magento\Framework\Phrase
   */
  public function getTabLabel() {
    return __('Moderators');
  }

  /**
   * Prepare title for tab
   *
   * @return \Magento\Framework\Phrase
   */
  public function getTabTitle() {
    return __('Moderators');
  }

  /**
   * {@inheritdoc}
   */
  public function canShowTab() {
    return true;
  }

  /**
   * {@inheritdoc}
   */
  public function isHidden() {
    return false;
  }

  public function getTabUrl() {
    return $this->getUrl('*/*/list/', ['_current' => true, 'isAjax' => 'true']);
  }
  
  public function getTabClass() {
    return 'ajax';
  }

}
