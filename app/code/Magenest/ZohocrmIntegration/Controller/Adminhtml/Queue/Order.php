<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue;

use Magenest\ZohocrmIntegration\Model\Queue;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Order
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync
 */
class Order extends \Magento\Backend\App\Action
{
    /**
     * @var
     */
    protected $orderFactory;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * @var string
     */
    protected $type = Queue::TYPE_ORDER;

    /**
     * @var int
     */
    protected $orderToInvoiceFlag;

    /**
     * Order constructor.
     * @param Context $context
     * @param OrderFactory $orderFactory
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        ScopeConfigInterface $scopeConfigInterface,
        QueueFactory $queueFactory
    ) {
        $this->queueFactory = $queueFactory;
        $this->orderFactory = $orderFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $orders = $this->orderFactory->create()->getCollection();
        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orders as $order) {
            $queue = $this->queueFactory->create();
            if (!$queue->queueExisted($this->type, $order->getIncrementId())) {
                $queue->enqueue($this->type, $order->getIncrementId());
            }
        }
        $this->messageManager->addSuccess(
            __('All Orders have been added to queue, you can delete items you do not want to sync or click Sync Now')
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
