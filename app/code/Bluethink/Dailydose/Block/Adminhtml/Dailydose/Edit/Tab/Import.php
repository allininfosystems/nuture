<?php
namespace Bluethink\Dailydose\Block\Adminhtml\Dailydose\Edit\Tab;
use Bluethink\Dailydose\Model\Config\Source\Socal;
class Import extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
    * @var \Bluethink\Trainingdiscount\Model\System\Config\Status
    */
     protected $_socialshare;
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
        \Magento\Store\Model\System\Store $systemStore,
        Socal $socialShare,
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
        $this->__socialshare = $socialShare;
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
        $model = $this->_coreRegistry->registry('dailydose_love');
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Dailydose Love')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }
        $fieldset->addField(
            'product_type',
            'select',
            array(
                'name' => 'product_type',
                'label' => __('product_type'),
                'title' => __('product_type'),
                'options'   => $this->__socialshare->toOptionArray()
            )
        );
        $fieldset->addField(
            'product_title',
            'text',
            array(
                'name' => 'product_title',
                'label' => __('product_title'),
                'title' => __('product_title'),
                /*'required' => true,*/
            )
        );
        $fieldset->addField(
            'product_url',
            'text',
            array(
                'name' => 'product_url',
                'label' => __('product_url'),
                'title' => __('product_url'),
                /*'required' => true,*/
            )
        );
        $fieldset->addField(
            'image',
            'image',
            
            array(
                'name' => 'image',
                'label' => __('Product Image'),
                'title' => __('Product Image'),
                'required' => true
            )
            );
        
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
        return __('Simple Product Import');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Simple Product Form');
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
