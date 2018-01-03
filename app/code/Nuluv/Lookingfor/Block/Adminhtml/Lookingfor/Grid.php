<?php
namespace Nuluv\Lookingfor\Block\Adminhtml\Lookingfor;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Nuluv\Lookingfor\Model\lookingforFactory
     */
    protected $_lookingforFactory;

    /**
     * @var \Nuluv\Lookingfor\Model\Status
     */
    protected $_status;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Nuluv\Lookingfor\Model\lookingforFactory $lookingforFactory
     * @param \Nuluv\Lookingfor\Model\Status $status
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Nuluv\Lookingfor\Model\LookingforFactory $LookingforFactory,
        \Nuluv\Lookingfor\Model\Status $status,
        \Magento\Framework\Module\Manager $moduleManager,
        array $data = []
    ) {
        $this->_lookingforFactory = $LookingforFactory;
        $this->_status = $status;
        $this->moduleManager = $moduleManager;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('postGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(false);
        $this->setVarNameFilter('post_filter');
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->_lookingforFactory->create()->getCollection();
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'type' => 'number',
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );


		
				$this->addColumn(
					'name',
					[
						'header' => __('Name'),
						'index' => 'name',
					]
				);
				
				$this->addColumn(
					'email',
					[
						'header' => __('Email'),
						'index' => 'email',
					]
				);
				
				$this->addColumn(
					'mobile',
					[
						'header' => __('Mobile'),
						'index' => 'mobile',
					]
				);
				
				$this->addColumn(
					'message',
					[
						'header' => __('Message'),
						'index' => 'message',
					]
				);
				


		
        //$this->addColumn(
            //'edit',
            //[
                //'header' => __('Edit'),
                //'type' => 'action',
                //'getter' => 'getId',
                //'actions' => [
                    //[
                        //'caption' => __('Edit'),
                        //'url' => [
                            //'base' => '*/*/edit'
                        //],
                        //'field' => 'id'
                    //]
                //],
                //'filter' => false,
                //'sortable' => false,
                //'index' => 'stores',
                //'header_css_class' => 'col-action',
                //'column_css_class' => 'col-action'
            //]
        //);
		

		
		   $this->addExportType($this->getUrl('lookingfor/*/exportCsv', ['_current' => true]),__('CSV'));
		   $this->addExportType($this->getUrl('lookingfor/*/exportExcel', ['_current' => true]),__('Excel XML'));

        $block = $this->getLayout()->getBlock('grid.bottom.links');
        if ($block) {
            $this->setChild('grid.bottom.links', $block);
        }

        return parent::_prepareColumns();
    }

	
    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {

        $this->setMassactionIdField('id');
        //$this->getMassactionBlock()->setTemplate('Nuluv_Lookingfor::lookingfor/grid/massaction_extended.phtml');
        $this->getMassactionBlock()->setFormFieldName('lookingfor');

        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl('lookingfor/*/massDelete'),
                'confirm' => __('Are you sure?')
            ]
        );

        $statuses = $this->_status->getOptionArray();

        $this->getMassactionBlock()->addItem(
            'status',
            [
                'label' => __('Change status'),
                'url' => $this->getUrl('lookingfor/*/massStatus', ['_current' => true]),
                'additional' => [
                    'visibility' => [
                        'name' => 'status',
                        'type' => 'select',
                        'class' => 'required-entry',
                        'label' => __('Status'),
                        'values' => $statuses
                    ]
                ]
            ]
        );


        return $this;
    }
		

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('lookingfor/*/index', ['_current' => true]);
    }

    /**
     * @param \Nuluv\Lookingfor\Model\lookingfor|\Magento\Framework\Object $row
     * @return string
     */
    public function getRowUrl($row)
    {
		
        return $this->getUrl(
            'lookingfor/*/edit',
            ['id' => $row->getId()]
        );
		
    }

	

}