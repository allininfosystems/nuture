<?php
namespace Bluethink\Productimport\Block\Adminhtml\Import\Edit\Tab;
class Import extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

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
        array $data = array()
    ) {
        $this->_systemStore = $systemStore;
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
        $model = $this->_coreRegistry->registry('productimport_import');
		$isElementDisabled = false;
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => __('Simple Product Import Using Csv')));

        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', array('name' => 'id'));
        }?>
        <?php
        $objectManager =  \Magento\Framework\App\ObjectManager::getInstance();        
        $storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
        $store = $storeManager->getStore();
        $baseurl=$store->getBaseUrl()."/csv.php";
        ?>
       <a href="<?php echo $baseurl; ?>">Download Sample Format</a>
       <?php
        $fieldset->addField(
            'csv_file',
            'file',
            array(
                'name' => 'csv_file',
                'label' => __('simple product Csv file'),
                'title' => __('simple product Csv file'),
                'required' => true,
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
