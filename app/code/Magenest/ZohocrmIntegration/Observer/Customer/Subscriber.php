<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ZohocrmIntegration extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ZohocrmIntegration
 * @author   ThaoPV
 */
namespace Magenest\ZohocrmIntegration\Observer\Customer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magenest\ZohocrmIntegration\Model\Sync\Lead;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Subscriber
 * @package Magenest\ZohocrmIntegration\Observer\Customer
 */
class Subscriber implements ObserverInterface
{
    /**
 #@+
     * Constants
     */
    const XML_PATH_ZOHOCRM_SUBSCRIBER = 'zohocrm/sync/subscriber';

    /**
     * Core store config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Lead
     */
    protected $_lead;

    /**
     * @param Lead                 $lead
     * @param ScopeConfigInterface $scopeConfig
     * @param RequestInterface     $request
     */
    public function __construct(
        Lead $lead,
        ScopeConfigInterface $scopeConfig,
        RequestInterface $request
    ) {
        $this->_lead        = $lead;
        $this->_request     = $request;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * Sync Subscriber to Lead
     *
     * @param  Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        if (!$this->_request->getFullActionName() == 'newsletter' || !$this->_scopeConfig->isSetFlag(self::XML_PATH_ZOHOCRM_SUBSCRIBER, ScopeInterface::SCOPE_STORE)) {
            return;
        }

        $email = (string) $this->_request->getPost('email');
        if ($email) {
            $this->_lead->syncByEmail($email);
        }
    }
}
