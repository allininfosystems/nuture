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

namespace WIO\Forum\Block\Adminhtml\Adminsettings\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

  protected function _prepareForm() {
    $model = $this->_coreRegistry->registry('admsettings_model');

    /*
     * Checking if user have permissions to save information
     */
    if ($this->_isAllowedAction('WIO_Forum::forum_save_adminsettings')) {
      $isElementDisabled = false;
    } else {
      $isElementDisabled = true;
    }

    /** @var \Magento\Framework\Data\Form $form */
    $form = $this->_formFactory->create();

    $form->setHtmlIdPrefix('forum_');

    $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Profile')]);
    
    $fieldset->addField(
      'avatar',
      'image',
      [
      'title' => __('Avatar'),
      'label' => __('Avatar'),
      'name' => 'avatar',
      'note' => 'Allow image type: jpg, jpeg, gif, png',
      ]
    );


    $fieldset->addField(
            'nickname', 'text', [
        'name' => 'nickname',
        'label' => __('Nickname'),
        'title' => __('Nickname'),
        'required' => true,
        'disabled' => $isElementDisabled
            ]
    );
    
    $fieldset->addField(
            'signature', 'textarea', [
        'name' => 'signature',
        'label' => __('Signature'),
        'title' => __('Signature'),
        'note' => '(' . htmlspecialchars(__('alowed tags: <b>, <ul>, <em>')) . ')',
        'disabled' => $isElementDisabled
            ]
    );

    /*if (!$model->getId()) {
      $model->setData('status', $isElementDisabled ? '0' : '1');
    }*/

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
    return __('Profile information');
  }

  /**
   * Prepare title for tab
   *
   * @return \Magento\Framework\Phrase
   */
  public function getTabTitle() {
    return __('Profile information');
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
