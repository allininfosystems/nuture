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

namespace WIO\Forum\Block\Adminhtml\Post\Edit\Tab;

class Main extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface {
  
  protected $_forumFactory;
  protected $_topicFactory;
  
  public function __construct(
    \Magento\Backend\Block\Template\Context $context, 
    \Magento\Framework\Registry $registry, 
    \Magento\Framework\Data\FormFactory $formFactory, 
    \WIO\Forum\Model\ForumFactory $forumFactory, 
    \WIO\Forum\Model\TopicFactory $topicFactory,
    array $data = []
  ) {
    parent::__construct($context, $registry, $formFactory, $data);
    $this->_forumFactory = $forumFactory->create()->getCollection();
    $this->_topicFactory = $topicFactory;
  }
  
  protected function _prepareForm() {
    $model = $this->_coreRegistry->registry('post_model');

    /*
     * Checking if user have permissions to save information
     */
    if ($this->_isAllowedAction('WIO_Forum::post_save')) {
      $isElementDisabled = false;
    } else {
      $isElementDisabled = true;
    }

    /** @var \Magento\Framework\Data\Form $form */
    $form = $this->_formFactory->create();

    $form->setHtmlIdPrefix('forum_');

    $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Post Information')]);

    if ($model->getId()) {
      $fieldset->addField('post_id', 'hidden', ['name' => 'post_id']);
    }

    $topics = $this->getParentTopics();
    
    $fieldset->addField('parent_id', 'select', array(
        'label' => __('Topic'),
        'name' => 'parent_id',
        'values' => $topics,
    ));
    
    $fieldset->addField(
            'status', 'select', [
        'label' => __('Status'),
        'title' => __('Post Status'),
        'name' => 'status',
        'required' => true,
        'options' => $model->getAvailableStatuses(),
        'disabled' => $isElementDisabled
            ]
    );
    
    $fieldset->addField(
            'post', 'textarea', [
        'label' => __('Post Data'),
        'title' => __('Post Data'),
        'name' => 'post',
        'id' => 'post',
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
    return __('Post Information');
  }

  /**
   * Prepare title for tab
   *
   * @return \Magento\Framework\Phrase
   */
  public function getTabTitle() {
    return __('Post Information');
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
  
  protected function getParentTopics() {
    $options = $this->_forumFactory->getSelectOptions();
    return $this->updateWithTopics($options);
  }
  
  protected function updateWithTopics($forumOptions) {
    $selectOptions = array();
    if(is_array($forumOptions)) {
      foreach($forumOptions as $forumId => $optionLabel){
        $selectOptions[$forumId] = [
          'label' => $optionLabel,
          'value' => $this->getTopicChilds($forumId)
        ];
      }
    }
    return $selectOptions;
  }
  
  protected function getTopicChilds($forumId) {
    $collection = $this->_topicFactory->create()
            ->getCollection();
    return $collection->byParent($forumId)->toOptionArray(); 
  }
  
}