<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue;

use Magenest\ZohocrmIntegration\Model\Queue;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Product
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue
 */
class Product extends \Magento\Backend\App\Action
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * @var string
     */
    protected $type = Queue::TYPE_PRODUCT;

    /**
     * Order constructor.
     * @param Context $context
     * @param ProductFactory $productFactory
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        QueueFactory $queueFactory
    ) {
        $this->queueFactory = $queueFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $products = $this->productFactory->create()->getCollection();
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($products as $product) {
            $queue = $this->queueFactory->create();
            if (!$queue->queueExisted($this->type, $product->getId())) {
                $queue->enqueue($this->type, $product->getId());
            }
        }
        $this->messageManager->addSuccess(
            __('All Products have been added to queue, you can delete items you do not want to sync or click Sync Now')
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
