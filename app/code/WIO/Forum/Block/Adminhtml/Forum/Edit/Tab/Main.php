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

namespace WIO\Forum\Block\Adminhtml\Forum\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

  /**
   * @var \Magento\Store\Model\System\Store
   */
  protected $_systemStore;
  protected $_customerGroup;

  /**
   * @param \Magento\Backend\Block\Template\Context $context
   * @param \Magento\Framework\Registry $registry
   * @param \Magento\Framework\Data\FormFactory $formFactory
   * @param \Magento\Store\Model\System\Store $systemStore
   * @param array $data
   */
  public function __construct(
  \Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Data\FormFactory $formFactory, \Magento\Store\Model\System\Store $systemStore, \Magento\Customer\Model\ResourceModel\Group\Collection $customerGroup, array $data = []
  ) {
    $this->_systemStore = $systemStore;
    $this->_customerGroup = $customerGroup;
    parent::__construct($context, $registry, $formFactory, $data);
  }

  protected function _prepareForm() {
    $model = $this->_coreRegistry->registry('forum_model');

    /*
     * Checking if user have permissions to save information
     */
    if ($this->_isAllowedAction('WIO_Forum::forum_save')) {
      $isElementDisabled = false;
    } else {
      $isElementDisabled = true;
    }

    /** @var \Magento\Framework\Data\Form $form */
    $form = $this->_formFactory->create();

    $form->setHtmlIdPrefix('forum_');

    $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Forum Information')]);

    if ($model->getId()) {
      $fieldset->addField('topic_id', 'hidden', ['name' => 'topic_id']);
    }

    $fieldset->addField(
            'title', 'text', [
        'name' => 'title',
        'label' => __('Forum Name'),
        'title' => __('Forum Name'),
        'required' => true,
        'disabled' => $isElementDisabled
            ]
    );

    $fieldset->addField(
            'url_text', 'text', [
        'name' => 'url_text',
        'label' => __('URL Key'),
        'title' => __('URL Key'),
        'class' => 'validate-identifier',
        'note' => __('Relative to Web Site Base URL and forum route'),
        'disabled' => $isElementDisabled
            ]
    );

    $field = $fieldset->addField(
            'customer_group_id', 'multiselect', [
        'name' => 'customer_group_id[]',
        'label' => __('Access Groups'),
        'title' => __('Access Groups'),
        'values' => $this->getCustomersGroups(),
        'disabled' => $isElementDisabled,
        'note' => __('Leave empty for all groups'),
            ]
    );

    /**
     * Check is single store mode
     */
    if (!$this->_storeManager->isSingleStoreMode()) {
      $field = $fieldset->addField(
              'store_id', 'select', [
          'name' => 'store_id',
          'label' => __('Store View'),
          'title' => __('Store View'),
          'required' => true,
          'values' => $this->_systemStore->getStoreValuesForForm(false, true),
          'disabled' => $isElementDisabled
              ]
      );
      $renderer = $this->getLayout()->createBlock(
              'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
      );
      $field->setRenderer($renderer);
    } else {
      $fieldset->addField(
              'store_id', 'hidden', ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
      );
      $model->setStoreId($this->_storeManager->getStore(true)->getId());
    }

    $fieldset->addField(
            'status', 'select', [
        'label' => __('Status'),
        'title' => __('Forum Status'),
        'name' => 'status',
        'required' => true,
        'options' => $model->getAvailableStatuses(),
        'disabled' => $isElementDisabled
            ]
    );

    $fieldset->addField(
            'description', 'textarea', [
        'label' => __('Description'),
        'title' => __('Forum Description'),
        'name' => 'description',
        'disabled' => $isElementDisabled
            ]
    );

    if (!$model->getId()) {
      $model->setData('status', $isElementDisabled ? '0' : '1');
    }

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
    return __('Forum Information');
  }

  /**
   * Prepare title for tab
   *
   * @return \Magento\Framework\Phrase
   */
  public function getTabTitle() {
    return __('Forum Information');
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

  protected function getCustomersGroups() {
    return $this->_customerGroup->toOptionArray();
  }

}
