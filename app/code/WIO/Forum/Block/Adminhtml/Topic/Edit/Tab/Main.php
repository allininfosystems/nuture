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

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {

  protected $_systemStore;
  protected $_forumFactory;

  public function __construct(
  \Magento\Backend\Block\Template\Context $context, 
  \Magento\Framework\Registry $registry, 
  \Magento\Framework\Data\FormFactory $formFactory, 
  \Magento\Store\Model\System\Store $systemStore, 
  \WIO\Forum\Model\ForumFactory $forumFactory, 
  array $data = []
  ) {
    $this->_systemStore = $systemStore;
    $this->_forumFactory = $forumFactory;
    parent::__construct($context, $registry, $formFactory, $data);
  }

  protected function _prepareForm() {
    $model = $this->_coreRegistry->registry('topic_model');
    $forumCollection = $this->_forumFactory->create()->getCollection();
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

    $form->setHtmlIdPrefix('forum_');

    $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Topic Information')]);

    if ($model->getId()) {
      $fieldset->addField('topic_id', 'hidden', ['name' => 'topic_id']);
    }

    $fieldset->addField(
            'title', 'text', [
        'name' => 'title',
        'label' => __('Topic Title'),
        'title' => __('Topic Title'),
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

    $fieldset->addField(
            'parent_id', 'select', [
        'label' => __('Parent Forum'),
        'title' => __('Parent Forum'),
        'name' => 'parent_id',
        'required' => true,
        'options' => $forumCollection->getSelectOptions(),
        'disabled' => $isElementDisabled
            ]
    );

    $fieldset->addField(
            'status', 'select', [
        'label' => __('Status'),
        'title' => __('Topic Status'),
        'name' => 'status',
        'required' => true,
        'options' => $model->getAvailableStatuses(),
        'disabled' => $isElementDisabled
            ]
    );

    $fieldset->addField(
            'description', 'textarea', [
        'label' => __('Description'),
        'title' => __('Topic Description'),
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
    return __('Topic Information');
  }

  /**
   * Prepare title for tab
   *
   * @return \Magento\Framework\Phrase
   */
  public function getTabTitle() {
    return __('Topic Information');
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
