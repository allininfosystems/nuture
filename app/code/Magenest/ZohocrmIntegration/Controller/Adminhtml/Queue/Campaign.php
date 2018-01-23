<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue;

use Magenest\ZohocrmIntegration\Model\Queue;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\CatalogRule\Model\RuleFactory;

/**
 * Class Campaign
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue
 */
class Campaign extends \Magento\Backend\App\Action
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
    protected $type = Queue::TYPE_CAMPAIGN;

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $_ruleFactory;

    /**
     * Order constructor.
     * @param Context $context
     * @param RuleFactory $ruleFactory
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        RuleFactory $ruleFactory,
        QueueFactory $queueFactory
    ) {
        $this->queueFactory = $queueFactory;
        $this->_ruleFactory = $ruleFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $rules = $this->_ruleFactory->create()->getCollection();
        /** @var \Magento\Catalog\Model\Product $product */
        foreach ($rules as $rule) {
            $queue = $this->queueFactory->create();
            if (!$queue->queueExisted($this->type, $rule->getId())) {
                $queue->enqueue($this->type, $rule->getId());
            }
        }
        $this->messageManager->addSuccess(
            __('All Campaigns have been added to queue, you can delete items you do not want to sync or click Sync Now')
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
