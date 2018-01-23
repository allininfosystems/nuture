<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue;

use Magenest\ZohocrmIntegration\Model\Queue;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\Order\InvoiceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Invoice
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue
 */
class Invoice extends \Magento\Backend\App\Action
{
    /**
     * @var
     */
    protected $invoiceFactory;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * @var string
     */
    protected $type = Queue::TYPE_INVOICE;

    /**
     * @var int
     */
    protected $invoiceToInvoiceFlag;

    /**
     * Invoice constructor.
     * @param Context $context
     * @param InvoiceFactory $invoiceFactory
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        InvoiceFactory $invoiceFactory,
        ScopeConfigInterface $scopeConfigInterface,
        QueueFactory $queueFactory
    ) {
        $this->queueFactory = $queueFactory;
        $this->invoiceFactory = $invoiceFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $invoices = $this->invoiceFactory->create()->getCollection();
        /** @var \Magento\Sales\Model\Order\Invoice $invoice */
        foreach ($invoices as $invoice) {
            $queue = $this->queueFactory->create();
            if (!$queue->queueExisted($this->type, $invoice->getIncrementId())) {
                $queue->enqueue($this->type, $invoice->getIncrementId());
            }
        }
        $this->messageManager->addSuccess(
            __('All Invoices have been added to queue, you can delete items you do not want to sync or click Sync Now')
        );
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->getUrl('*/*/index'));
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ZohocrmIntegration::config_zohocrm');
    }
}
