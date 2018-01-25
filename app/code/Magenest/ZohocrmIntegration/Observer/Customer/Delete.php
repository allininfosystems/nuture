<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ZohocrmIntegration\Observer\Customer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magenest\ZohocrmIntegration\Model\Data as Data;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magenest\ZohocrmIntegration\Model\Sync\Lead;
use Magenest\ZohocrmIntegration\Model\Sync\Contact;
use Magenest\ZohocrmIntegration\Model\Sync\Account;

/**
 * Class Delete Observer
 *
 * @package Magenest\ZohocrmIntegration\Observer\Customer
 */
class Delete implements ObserverInterface
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
     * @var \Magenest\ZohocrmIntegration\Model\Sync\Account
     */
    protected $_account;


    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param \Magenest\ZohocrmIntegration\Model\Sync\Lead $lead
     * @param \Magenest\ZohocrmIntegration\Model\Sync\Contact $contact
     * @param \Magenest\ZohocrmIntegration\Model\Sync\Account $account
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Lead $lead,
        Contact $contact,
        Account $account
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_lead        = $lead;
        $this->_contact     = $contact;
        $this->_account     = $account;
    }

    /**
     * Admin delete a customer
     *
     * @param  Observer $observer
     * @return string
     */
    public function execute(Observer $observer)
    {
        try {
            $customer = $observer->getEvent()->getCustomer();
            $email    = $customer->getEmail();
            if ($this->_scopeConfig->isSetFlag(Data::XML_PATH_ALLOW_SYNC_LEAD, ScopeInterface::SCOPE_STORE)) {
                $this->_lead->delete($email);
            }

            if ($this->_scopeConfig->isSetFlag(Data::XML_PATH_ALLOW_SYNC_CONTACT, ScopeInterface::SCOPE_STORE)) {
                $this->_contact->delete($email);
            }

            if ($this->_scopeConfig->isSetFlag(Data::XML_PATH_ALLOW_SYNC_ACCOUNT, ScopeInterface::SCOPE_STORE)) {
                $this->_account->delete($email);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
