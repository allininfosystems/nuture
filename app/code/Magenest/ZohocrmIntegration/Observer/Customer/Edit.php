<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ZohocrmIntegration\Observer\Customer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\ZohocrmIntegration\Model\Data as Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magenest\ZohocrmIntegration\Model\Sync\Lead;
use Magenest\ZohocrmIntegration\Model\Sync\Contact;

/**
 * Class Update
 */
class Edit implements ObserverInterface
{
    /**
     * Core Config Data
     *
     * @var $_scopeConfig \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magenest\ZohocrmIntegration\Model\Sync\Lead
     */
    protected $_lead;

    /**
     * @var \Magenest\ZohocrmIntegration\Model\Sync\Contact
     */
    protected $_contact;


    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param Lead $lead
     * @param Contact $contact
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Lead $lead,
        Contact $contact
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_lead        = $lead;
        $this->_contact     = $contact;
    }

    /**
     * Admin/Cutomer edit information address
     *
     * @param  Observer $observer
     * @return string
     */
    public function execute(Observer $observer)
    {
        try {
            $event    = $observer->getEvent();
            /** @var \Magento\Customer\Model\Customer $customer */
            $customer = $event->getCustomer();
            $id       = $customer->getId();
            if ($this->_scopeConfig->isSetFlag(Data::XML_PATH_ALLOW_SYNC_LEAD, ScopeInterface::SCOPE_STORE)) {
                $this->_lead->sync($id);
            }

            if ($this->_scopeConfig->isSetFlag(Data::XML_PATH_ALLOW_SYNC_CONTACT, ScopeInterface::SCOPE_STORE)) {
                $this->_contact->sync($id);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
