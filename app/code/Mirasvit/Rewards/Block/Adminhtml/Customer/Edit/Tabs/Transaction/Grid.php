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



namespace Mirasvit\Rewards\Block\Adminhtml\Customer\Edit\Tabs\Transaction;

use Magento\Customer\Controller\RegistryConstants;

class Grid extends \Magento\Backend\Block\Widget\Grid\Extended
{
    /**
     * @var \Mirasvit\Rewards\Model\TransactionFactory
     */
    protected $transactionFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Backend\Block\Widget\Context
     */
    protected $context;

    /**
     * @var \Magento\Backend\Helper\Data
     */
    protected $backendHelper;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    private $customerRepository;

    /**
     * @param \Mirasvit\Rewards\Model\TransactionFactory $transactionFactory
     * @param \Magento\Framework\Registry                $registry
     * @param \Magento\Backend\Block\Widget\Context      $context
     * @param \Magento\Backend\Helper\Data               $backendHelper
     * @param \Magento\Customer\Model\Customer           $customerRepository
     * @param array                                      $data
     */
    public function __construct(
        \Mirasvit\Rewards\Model\TransactionFactory $transactionFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Customer\Model\Customer $customerRepository,
        array $data = []
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->registry           = $registry;
        $this->context            = $context;
        $this->backendHelper      = $backendHelper;
        $this->customerRepository = $customerRepository;

        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('transactionGrid');
        $this->setDefaultSort('transaction_id');
        $this->setDefaultDir('DESC');

        $this->setUseAjax(true);

        $this->setEmptyText(__('No Records Found'));
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('rewards/transaction/customergrid', ['_current' => true]);
    }

    /**
     * @return $this
     */
    protected function _prepareCollection()
    {
        $customer = $this->_getCustomer();
        $collection = $this->transactionFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id', $customer->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', [
                'header' => __('ID'),
                'index' => 'transaction_id',
                'filter_index' => 'main_table.transaction_id',
            ]
        );

        $this->addColumn('amount', [
                'header' => __('Balance Change'),
                'index' => 'amount',
                'filter_index' => 'main_table.amount',
            ]
        );
        $this->addColumn('comment', [
                'header' => __('Comment'),
                'index' => 'comment',
                'filter_index' => 'main_table.comment',
            ]
        );
        $this->addColumn('created_at', [
                'header' => __('Created At'),
                'index' => 'created_at',
                'filter_index' => 'main_table.created_at',
                'type' => 'date',
            ]
        );
        $this->addColumn('expires_at', [
                'header' => __('Expires At'),
                'index' => 'expires_at',
                'filter_index' => 'main_table.expires_at',
                'type' => 'date',
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * @return \Magento\Customer\Model\Customer|bool
     */
    protected function _getCustomer()
    {
        if ($customerId = $this->registry->registry(RegistryConstants::CURRENT_CUSTOMER_ID)) {
            $customerData = $this->customerRepository->load($customerId);

            return $customerData;
        }

        return false;
    }
}
