<?php
/**
 * Created by PhpStorm.
 * User: canhnd
 * Date: 13/02/2017
 * Time: 15:15
 */
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync;

use Magenest\ZohocrmIntegration\Model\Sync;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Class Invoice
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync
 */
class Invoice extends Action
{
    /**
     * @var Sync\SalesOrder
     */
    protected $_invoice;

    /**
     * Invoice constructor.
     * @param Context $context
     * @param Sync\Invoice $invoice
     */
    public function __construct(
        Context $context,
        Sync\Invoice $invoice
    ) {
        $this->_invoice = $invoice;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        try {
            $invoiceIncrementId = $this->getRequest()->getParam('id');
            if ($invoiceIncrementId) {
                $this->_invoice->sync($invoiceIncrementId);
                $this->messageManager->addSuccess(
                    __('Invoice is synced successfully')
                );
            } else {
                $this->messageManager->addNotice(
                    __('No Invoice has been selected')
                );
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('Something happen during syncing process. Detail: ' . $e->getMessage())
            );
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ZohocrmIntegration::config_zohocrm');
    }
}
