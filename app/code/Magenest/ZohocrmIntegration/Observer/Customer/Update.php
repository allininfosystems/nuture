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
 * Class Update Customer Information Address
 *
 * @package Magenest\ZohocrmIntegration\Observer\Customer
 */
class Update implements ObserverInterface
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
     * Cutomer edit information address
     *
     * @param  Observer $observer
     * @return string
     */
    public function execute(Observer $observer)
    {
        try {
            $event   = $observer->getEvent();
            $address = $event->getCustomerAddress();
            $id      = $address->getCustomer()->getId();
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
