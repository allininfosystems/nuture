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

namespace WIO\Forum\Block\Adminhtml\Post\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic {

  /**
   * Prepare form
   *
   * @return $this
   */
  protected function _prepareForm() {
    /** @var \Magento\Framework\Data\Form $form */
    $form = $this->_formFactory->create(
            ['data' => ['id' => 'edit_form', 'onsubmit'=> 'return ___triggerTinyMce(this);', 'action' => $this->getData('action'), 'method' => 'post']]
    );
    $form->setUseContainer(true);
    $this->setForm($form);
    return parent::_prepareForm();
  }

}
