<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Block\Adminhtml\Transaction;

use Mirasvit\Rewards\Model\Config\Source\Customer\Group as GroupOptions;
use Magento\Store\Model\StoreManagerInterface;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var array
     */
    protected $_customFilters = [];
    /**
     * @var array
     */
    protected $_removeFilters = [];

    public function __construct(
        GroupOptions $groupOptions,
        \Mirasvit\Rewards\Model\TransactionFactory $transactionFactory,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        array $data = []
    ) {
        $this->groupOptions       = $groupOptions;
        $this->transactionFactory = $transactionFactory;
        $this->storeManager       = $context->getStoreManager();
        $this->context            = $context;
        $this->backendHelper      = $backendHelper;

        parent::__construct($context, $backendHelper, $data);

        $this->addExportType('rewards/transaction/export/type/csv', 'csv');
        $this->addExportType('rewards/transaction/export/type/xml', 'xml');
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('grid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * @param string      $field
     * @param bool|string $filter
     * @return $this
     */
    public function addCustomFilter($field, $filter = false)
    {
        if ($filter) {
            $this->_customFilters[$field] = $filter;
        } else {
            $this->_customFilters[] = $field;
        }

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function removeFilter($field)
    {
        $this->_removeFilters[$field] = true;

        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $collection = $this->transactionFactory->create()
            ->getCollection()
            ->joinCustomerName()
            ->joinCustomerGroup()
        ;

        foreach ($this->_customFilters as $key => $value) {
            if ((int) $key === $key && is_string($value)) {
                $collection->getSelect()->where($value);
            } else {
                $collection->addFieldToFilter($key, $value);
            }
        }
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', [
                'header'        => __('ID'),
                'header_export' => 'transaction_id',
                'index'         => 'transaction_id',
                'filter_index'  => 'main_table.transaction_id',
            ]
        );
        $this->addColumn('customer_name', [
                'header'        => __('Customer Name'),
                'header_export' => 'customer_name',
                'index'         => 'customer_name',
            ]
        );
        $this->addColumn('customer_email', [
                'header'        => __('Customer Email'),
                'header_export' => 'customer_email',
                'index'         => 'customer_email',
                'filter_index'  => 'customer.email',
            ]
        );

        $this->addColumn('customer_group_name', [
                'header'        => __('Customer Group'),
                'header_export' => 'customer_group_name',
                'index'         => 'customer_group_name',
                'filter_index'  => 'customer.group_id',
                'type'          => 'options',
                'options'       => $this->groupOptions->toOptionArray(),
            ]
        );
        if ($this->_isExport) {
            $this->addColumn('amount', [
                    'header'        => __('Balance Change'),
                    'header_export' => 'amount',
                    'index'         => 'amount',
                    'filter_index'  => 'main_table.amount',
                    'type'          => 'text',
                ]
            );
        } else {
            $this->addColumn('amount', [
                    'header'       => __('Balance Change'),
                    'index'        => 'amount',
                    'filter_index' => 'main_table.amount',
                    'type'         => 'currency',
                    'renderer'     => '\Mirasvit\Rewards\Block\Adminhtml\Grid\Renderer\Balance',
                ]
            );
        }
        $this->addColumn('comment', [
                'header'        => __('Comment'),
                'header_export' => 'comment',
                'index'         => 'comment',
                'filter_index'  => 'main_table.comment',
            ]
        );
        $this->addColumn('created_at', [
                'header'        => __('Created At'),
                'header_export' => 'created_at',
                'index'         => 'created_at',
                'filter_index'  => 'main_table.created_at',
                'type'          => $this->_isExport ? 'string' : 'date',
            ]
        );
        $this->addColumn('expires_at', [
                'header'        => __('Expires At'),
                'header_export' => 'expires_at',
                'index'         => 'expires_at',
                'filter_index'  => 'main_table.expires_at',
                'type'          => $this->_isExport ? 'string' : 'date',
            ]
        );
        $this->addColumn('action',
            [
                'header'  => __('Action'),
                'width'   => '100',
                'type'    => 'action',
                'getter'  => 'getId',
                'actions' => [
                    [
                        'caption' => __('Delete'),
                        'url'     => ['base' => '*/*/delete'],
                        'field'   => 'id',
                    ],
                ],
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ]
        );

        if ($this->_isExport) {
            $this->addColumn('code', [
                    'header'        => __('Code'),
                    'header_export' => 'code',
                    'index'         => 'code',
                ]
            );
            $this->addColumn('is_expired', [
                    'header'        => __('Is expired'),
                    'header_export' => 'is_expired',
                    'index'         => 'is_expired',
                ]
            );
            $this->addColumn('is_expiration_email_sent', [
                    'header'        => __('Is sent'),
                    'header_export' => 'is_expiration_email_sent',
                    'index'         => 'is_expiration_email_sent',
                ]
            );
            $this->addColumn('amount_used', [
                    'header'        => __('Amount Used'),
                    'header_export' => 'amount_used',
                    'index'         => 'amount_used',
                ]
            );
            $this->addColumn('website_id', [
                'header_export'  => 'website_id',
                'index'          => 'website_id',
                'frame_callback' => [$this, 'websiteCode'],
            ]);
        }

        return parent::_prepareColumns();
    }

    /**
     * @param float                         $value
     * @param \Magento\Framework\DataObject $row
     * @param \Magento\Framework\DataObject $column
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function websiteCode($value, $row, $column)
    {
        return $this->storeManager->getWebsite($value)->getCode();
    }

    /**
     * @return $this
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('transaction_id');
        $this->getMassactionBlock()->setFormFieldName('transaction_id');
        $this->getMassactionBlock()->addItem('delete', [
            'label' => __('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => __('Are you sure?'),
        ]);

        return $this;
    }

    /************************/
}
