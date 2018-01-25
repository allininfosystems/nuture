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

/**
 * Class Register Observer
 *
 * @package Magenest\ZohocrmIntegration\Observer\Customer
 */
class Register implements ObserverInterface
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
     * @param ScopeConfigInterface $scopeConfig
     * @param Lead $lead
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Lead $lead
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_lead        = $lead;
    }

    /**
     * Customer register success event
     *
     * @param  Observer $observer
     * @return void|string
     */
    public function execute(Observer $observer)
    {
        if (!$this->_scopeConfig->isSetFlag(Data::XML_PATH_ALLOW_SYNC_LEAD, ScopeInterface::SCOPE_STORE)) {
            return;
        }

        try {
            $event    = $observer->getEvent();
            $customer = $event->getCustomer();
            $id       = $customer->getId();
            $this->_lead->sync($id);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
