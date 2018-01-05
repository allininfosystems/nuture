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

namespace WIO\Forum\Block\Adminhtml\Topic\Edit\Tab;

class MetaData extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

  protected function _prepareForm() {
    /* @var $model \Magento\Cms\Model\Page */
    $model = $this->_coreRegistry->registry('topic_model');

    /*
     * Checking if user have permissions to save information
     */
    if ($this->_isAllowedAction('WIO_Forum::topic_save')) {
      $isElementDisabled = false;
    } else {
      $isElementDisabled = true;
    }

    /** @var \Magento\Framework\Data\Form $form */
    $form = $this->_formFactory->create();

    $form->setHtmlIdPrefix('topic_');

    $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Topic Meta Data')]);

    $fieldset->addField(
            'meta_description', 'textarea', [
        'label' => __('Meta Description'),
        'title' => __('Meta Description'),
        'name' => 'meta_description',
        'disabled' => $isElementDisabled
            ]
    );


    $fieldset->addField(
            'meta_keywords', 'textarea', [
        'label' => __('Meta Keywords'),
        'title' => __('Meta Keywords'),
        'name' => 'meta_keywords',
        'disabled' => $isElementDisabled
            ]
    );


    $form->setValues($model->getData());
    $this->setForm($form);

    return parent::_prepareForm();
  }

  /**
   * Prepare label for tab
   *
   * @return \Magento\Framework\Phrase
   */
  public function getTabLabel() {
    return __('Topic Meta Data');
  }

  /**
   * Prepare title for tab
   *
   * @return \Magento\Framework\Phrase
   */
  public function getTabTitle() {
    return __('Topic Meta Data');
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

  /**
   * Check permission for passed action
   *
   * @param string $resourceId
   * @return bool
   */
  protected function _isAllowedAction($resourceId) {
    return $this->_authorization->isAllowed($resourceId);
  }

}
