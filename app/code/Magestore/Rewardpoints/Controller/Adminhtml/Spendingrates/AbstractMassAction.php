<?php
namespace Magestore\Rewardpoints\Controller\Adminhtml\Spendingrates;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magestore\Rewardpoints\Model\ResourceModel\Rate\CollectionFactory;
use Magestore\Rewardpoints\Model\ResourceModel\Rate\Collection;

/**
 * Class AbstractMassStatus
 */
abstract class AbstractMassAction extends \Magento\Backend\App\Action
{
    /**
     * @var string
     */
    protected $redirectUrl = '*/*/index';

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Check the permission to Manage Customers
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Rewardpoints::Spending_Rates');
    }

    /**
     * Return component referer url
     * TODO: Technical dept referer url should be implement as a part of Action configuration in in appropriate way
     *
     * @return null|string
     */
    protected function getComponentRefererUrl()
    {
        return '*/*/index';
    }

    /**
     * Execute action to collection items
     *
     * @param AbstractCollection $collection
     * @return ResponseInterface|ResultInterface
     */
    abstract protected function massAction(Collection $collection);
}
