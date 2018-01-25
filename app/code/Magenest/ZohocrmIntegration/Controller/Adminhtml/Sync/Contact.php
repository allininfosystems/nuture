<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync;

use Magenest\ZohocrmIntegration\Model\Sync;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;

/**
 * Class Contact
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync
 */
class Contact extends Action
{
    /**
     * @var Sync\Contact
     */
    protected $_contact;

    /**
     * Customer constructor.
     * @param Context $context
     * @param Sync\Contact $contact
     */
    public function __construct(
        Context $context,
        Sync\Contact $contact
    ) {
        $this->_contact = $contact;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        try {
            $customerId = $this->getRequest()->getParam('id');
            if ($customerId) {
                $this->_contact->sync($customerId, true);
                $this->messageManager->addSuccess(
                    __('Contact is synced successfully')
                );
            } else {
                $this->messageManager->addNotice(
                    __('No contact has been selected')
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
