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
 * Class Lead using to sync Leads table
 *
 * @package Magenest\ZohocrmIntegration\Model\Sync
 */
class Lead extends Connector
{
    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;
    /**
     * @var QueueFactory
     */
    protected $_queueFactory;
    
    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConfig $resourceConfig
     * @param ZendClientFactory $httpClientFactory
     * @param Data $data
     * @param ReportFactory $reportFactory
     * @param Customer $customer
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        ResourceConfig $resourceConfig,
        ZendClientFactory $httpClientFactory,
        Data $data,
        QueueFactory $queueFactory,
        ReportFactory $reportFactory,
        Customer $customer
    ) {
        parent::__construct($scopeConfig, $resourceConfig, $httpClientFactory, $reportFactory);
        $this->_logger = $logger;
        $this->_type     = 'Leads';
        $this->_table    = 'customer';
        $this->_data     = $data;
        $this->_customer = $customer;
        $this->_queueFactory = $queueFactory;
    }

    /**
     * Update or create new a record
     *
     * @param  int $id
     * @return string
     */
    public function sync($id)
    {
        $model     = $this->_customer->load($id);
        $email     = $model->getEmail();
        $firstname = $model->getFirstname();
        $lastname  = $model->getLastname();

        $params  = $this->_data->getCustomer($model, $this->_type);
        $params += [
                    'Last Name'  => $lastname,
                    'First Name' => $firstname,
                    'Email'      => $email,
                   ];

        // Format XML send data
        $postXml = '<Leads><row no="1">';
        foreach ($params as $key => $value) {
            $postXml .= '<FL val="'.$key.'">'.$value.'</FL>';
        }

        $postXml .= '</row></Leads>';
        $id       = $this->insertRecords($this->_type, $postXml);

        return $id;
    }

    /**
     * If customer not register
     *
     * @param  array $data
     * @return string
     */
    public function syncByEmail($data)
    {
        $params = [
            'Last Name'  => 'Guest',
            'First Name' => 'Guest',
            'Email' => $data,
        ];
        // Format XML send data
        $postXml = '<Leads><row no="1">';
        foreach ($params as $key => $value) {
            $postXml .= '<FL val="'.$key.'">'.trim($value).'</FL>';
        }

        $postXml .= '</row></Leads>';

        $id = $this->insertRecords($this->_type, $postXml);

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
     * Sync all old lead data
     *
     * @return string
     */
    public function syncAllQueue()
    {
        $collections = $this->_queueFactory->create()
            ->getCollection()
            ->addFieldToFilter('type', 'Lead')
            ->getData();

        if ($collections) {
            $insertRecord = $this->getAllLead($collections);
            $id       = $this->insertRecords($this->_type, $insertRecord);

            return $id;
        }
    }

    /**
     * Get All Lead Record
     *
     * @param $collections
     * @return string
     */
    public function getAllLead($collections)
    {
        $postStartXml = '<Leads>';
        $postEndXml = '</Leads>';

        $numberConfig = $this->_scopeConfig->getValue('zohocrm/sync/number', ScopeInterface::SCOPE_STORE);

        $number = 1;
        foreach ($collections as $collection) {
            if ($number <= $numberConfig) {
                $model     = $this->_customer->load($collection['entity_id']);
                $email     = $model->getEmail();
                $firstname = $model->getFirstname();
                $lastname  = $model->getLastname();

                $params  = $this->_data->getFullCustomer($collection['entity_id'], $this->_type);
                $params += [
                    'Last Name'  => $lastname,
                    'First Name' => $firstname,
                    'Email'      => $email,
                ];

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
