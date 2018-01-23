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
namespace Magenest\ZohocrmIntegration\Model\Sync;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Framework\HTTP\ZendClientFactory;
use Magenest\ZohocrmIntegration\Model\ReportFactory;
use Magento\Customer\Model\Customer;
use Magenest\ZohocrmIntegration\Model\Connector;
use Magenest\ZohocrmIntegration\Model\Data;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Account
 * Sync Customer to Account table
 *
 * @package Magenest\ZohocrmIntegration\Model\Sync
 */
class Account extends Connector
{
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var QueueFactory
     */
    protected $_queueFactory;

    protected $_logger;
    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConfig $resourceConfig
     * @param ZendClientFactory $httpClientFactory
     * @param Data $data
     * @param ReportFactory $reportFactory
     * @param Customer $customer
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        ResourceConfig $resourceConfig,
        ZendClientFactory $httpClientFactory,
        Data $data,
        ReportFactory $reportFactory,
        QueueFactory $queueFactory,
        Customer $customer
    ) {
        parent::__construct($scopeConfig, $resourceConfig, $httpClientFactory, $reportFactory);
        $this->_type     = 'Accounts';
        $this->_table    = 'customer';
        $this->_data     = $data;
        $this->_customer = $customer;
        $this->_queueFactory = $queueFactory;
        $this->_logger = $logger;
    }

    /**
     * Create/Update a record in Account tables
     *
     * @param  $id
     * @return string
     */
    public function sync($id)
    {
        $model   = $this->_customer->load($id);
        $email   = $model->getEmail();
        $params  = $this->_data->getCustomer($model, $this->_type);
        $params += ['Account Name' => $email];

        // Format XML send data
        $postXml = '<Accounts><row no="1">';
        foreach ($params as $key => $value) {
            $postXml .= '<FL val="'.$key.'">'.$value.'</FL>';
        }

        $postXml .= '</row></Accounts>';
        $id       = $this->insertRecords($this->_type, $postXml);

        return $id;
    }

    /**
     * Sync by email if customer not register
     *
     * @param  $email
     * @return string
     */
    public function syncByEmail($email)
    {

        $params = ['Account Name' => $email];

        // Format XML send data
        $postXml = '<Accounts><row no="1">';
        foreach ($params as $key => $value) {
            $postXml .= '<FL val="'.$key.'">'.trim($value).'</FL>';
        }

        $postXml .= '</row></Accounts>';
        $id       = $this->insertRecords($this->_type, $postXml);

        return $id;
    }

    /**
     * Delete Record
     *
     * @param string $email
     */
    public function delete($email)
    {
        $id = $this->searchRecords($this->_type, $email);
        if ($id) {
            $this->deleteRecords($this->_type, $id);
        }

        return;
    }

    /**
     * Sync all old data Accounts
     *
     * @return string
     */
    public function syncAllQueue()
    {
        $collections = $this->_queueFactory->create()
            ->getCollection()
            ->addFieldToFilter('type', 'Account')
            ->getData();

        if ($collections) {
            $syncAccounts = $this->getAllAccount($collections);
            
            $all       = $this->insertRecords($this->_type, $syncAccounts);
                
            return $all;
        }
    }

    public function getAllAccount($collections)
    {
        $postStartXml = '<Accounts>';
        $postEndXml = '</Accounts>';

        $numberConfig = $this->_scopeConfig->getValue('zohocrm/sync/number', ScopeInterface::SCOPE_STORE);

        $number = 1;
        foreach ($collections as $collection) {
            if ($number <= $numberConfig) {
                $model   = $this->_customer->load($collection['entity_id']);
                $email   = $model->getEmail();
                $params  = $this->_data->getFullCustomer($collection['entity_id'], $this->_type);
                $params += ['Account Name' => $email];

                // Format XML send data
                $postXml = '<row no="'.$number.'">';
                foreach ($params as $key => $value) {
                    $postXml .= '<FL val="'.$key.'">'.$value.'</FL>';
                }

                $postXml .= '</row>';
            }

            $number++;
            $postStartXml .= $postXml;

            $queue = $this->_queueFactory->create()->load($collection['id']);
            $queue->delete();
        }

        $postStartXml.= $postEndXml;

        return $postStartXml;
    }
}
