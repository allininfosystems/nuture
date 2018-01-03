<?php
namespace Bluethink\Faq\Block\Adminhtml\Faq\Edit\Tab;
class FAQ extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;
    protected $_allowedAttribute;
     protected $_wysiwygConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Bluethink\Faq\Model\System\Config\Status $allowedAttribute,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->_allowedAttribute = $allowedAttribute;
        $this->_wysiwygConfig = $wysiwygConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
		/* @var $model \Magento\Cms\Model\Page */
        $model = $this->_coreRegistry->registry('faq_faq');
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('FAQ')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }

		$fieldset->addField(
            'questions',
            'text',
            array(
                'name' => 'questions',
                'label' => __('Questions'),
                'title' => __('Questions'),
                /*'required' => true,*/
            )
        );
		$fieldset->addField(
            'answers',
            'editor',
            array(
                'name' => 'answers',
                'label' => __('Answers'),
                'title' => __('Answers'),
                'style' => 'height:10em',
                'config' => $this->_wysiwygConfig->getConfig()
                /*'required' => true,*/
            )
        );

         $fieldset->addField(
            'assigned_category',
            'select',
            array(
                'name' => 'assigned_category',
                'label' => __('Category Name'),
                'title' => __('Category Name'),
                'values' => $this->_allowedAttribute->toOptionArray(),
                /*'required' => true,*/
            )
        );
	/*	$fieldset->addField(
            'assigned_category',
            'text',
            array(
                'name' => 'assigned_category',
                'label' => __('Assigned Category'),
                'title' => __('Assigned Category'),
                
            )
        );*/
		/*{{CedAddFormField}}*/
        
        if (!$model->getId()) {
            $model->setData('status', $isElementDisabled ? '2' : '1');
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();   
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('FAQ');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('FAQ');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
