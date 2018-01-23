<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue;

use Magenest\ZohocrmIntegration\Model\Queue;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\InvoiceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Config\Model\ResourceModel\Config;
use Magento\CatalogRule\Model\RuleFactory;

/**
 * Class All
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue
 */
class All extends \Magento\Backend\App\Action
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $_configInterface;

    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var InvoiceFactory
     */
    protected $invoiceFactory;

    /**
     * All constructor.
     * @param Context $context
     * @param OrderFactory $orderFactory
     * @param CustomerFactory $customerFactory
     * @param ProductFactory $productFactory
     * @param Config $config
     * @param ScopeConfigInterface $configInterface
     * @param RuleFactory $ruleFactory
     * @param InvoiceFactory $invoiceFactory
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        CustomerFactory $customerFactory,
        ProductFactory $productFactory,
        Config $config,
        ScopeConfigInterface $configInterface,
        RuleFactory $ruleFactory,
        InvoiceFactory $invoiceFactory,
        QueueFactory $queueFactory
    ) {
        $this->invoiceFactory = $invoiceFactory;
        $this->customerFactory = $customerFactory;
        $this->productFactory = $productFactory;
        $this->orderFactory = $orderFactory;
        $this->queueFactory = $queueFactory;
        $this->ruleFactory = $ruleFactory;
        $this->_config = $config;
        $this->_configInterface = $configInterface;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $this->enqueueAccounts();

        $this->enqueueCampaigns();

        $this->enqueueContacts();

        $this->enqueueOrders();

        $this->enqueueProducts();

        $this->enqueueLeads();

        $this->enqueueInvoices();

        $this->messageManager->addSuccess(
            __('All Data have been added to queue, you can delete items you do not want to sync or click Sync Now')
        );
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->getUrl('*/*/index'));
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ZohocrmIntegration::config_zohocrm');
    }

    protected function enqueueAccounts()
    {
        $customers = $this->customerFactory->create()->getCollection();
        /** @var \Magento\Customer\Model\Customer $customer */
        foreach ($customers as $customer) {
            $queue = $this->queueFactory->create();
            if (!$queue->queueExisted(Queue::TYPE_ACCOUNT, $customer->getId())) {
                $queue->enqueue(Queue::TYPE_ACCOUNT, $customer->getId());
            }
        }
    }

    protected function enqueueContacts()
    {
        $customers = $this->customerFactory->create()->getCollection();
        /** @var \Magento\Customer\Model\Customer $customer */
        foreach ($customers as $customer) {
            $queue = $this->queueFactory->create();
            if (!$queue->queueExisted(Queue::TYPE_CONTACT, $customer->getId())) {
                $queue->enqueue(Queue::TYPE_CONTACT, $customer->getId());
            }
        }
    }

    protected function enqueueLeads()
    {
        $customers = $this->customerFactory->create()->getCollection();
        /** @var \Magento\Customer\Model\Customer $customer */
        foreach ($customers as $customer) {
            $queue = $this->queueFactory->create();
            if (!$queue->queueExisted(Queue::TYPE_LEAD, $customer->getId())) {
                $queue->enqueue(Queue::TYPE_LEAD, $customer->getId());
            }
        }
    }

    protected function enqueueProducts()
    {
        $products = $this->productFactory->create()->getCollection();
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($products as $product) {
            $queue = $this->queueFactory->create();
            if (!$queue->queueExisted(Queue::TYPE_PRODUCT, $product->getId())) {
                $queue->enqueue(Queue::TYPE_PRODUCT, $product->getId());
            }
        }
    }

    protected function enqueueOrders()
    {
        $orders = $this->orderFactory->create()->getCollection();
        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orders as $order) {
            $queue = $this->queueFactory->create();
            if (!$queue->queueExisted(Queue::TYPE_ORDER, $order->getIncrementId())) {
                $queue->enqueue(Queue::TYPE_ORDER, $order->getIncrementId());
            }
        }
    }

    protected function enqueueCampaigns()
    {
        $rules = $this->ruleFactory->create()->getCollection();
        /** @var \Magento\CatalogRule\Model\Rule $rule */
        foreach ($rules as $rule) {
            $queue = $this->queueFactory->create();
            if (!$queue->queueExisted(Queue::TYPE_CAMPAIGN, $rule->getId())) {
                $queue->enqueue(Queue::TYPE_CAMPAIGN, $rule->getId());
            }
        }
    }

    protected function enqueueInvoices()
    {
        $invoices = $this->invoiceFactory->create()->getCollection();
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        foreach ($invoices as $invoice) {
            $queue = $this->queueFactory->create();
            if (!$queue->queueExisted(Queue::TYPE_INVOICE, $invoice->getIncrementId())) {
                $queue->enqueue(Queue::TYPE_INVOICE, $invoice->getIncrementId());
            }
        }
    }
}
