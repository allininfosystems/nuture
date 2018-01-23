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
use Magento\CatalogRule\Model\Rule;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Campaign
 * Sync Catalog Rules Prices to Campaign
 *
 * @package Magenest\ZohocrmIntegration\Model\Sync
 */
class Campaign extends Connector
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

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
     * @param Rule $rule
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ResourceConfig $resourceConfig,
        ZendClientFactory $httpClientFactory,
        Data $data,
        QueueFactory $queueFactory,
        ReportFactory $reportFactory,
        Rule $rule
    ) {
        parent::__construct($scopeConfig, $resourceConfig, $httpClientFactory, $reportFactory);
        $this->_type  = 'Campaigns';
        $this->_table = 'catalogrule';
        $this->_data  = $data;
        $this->_rule  = $rule;
        $this->_queueFactory = $queueFactory;
    }

    /**
     * Create new a record
     *
     * @param  int $id
     * @return string
     */
    public function sync($id)
    {
        $model   = $this->_rule->load($id);
        $name    = $model->getName();
        $params  = $this->_data->getCampaign($model, $this->_type);
        $params += [
                    'Campaign Name' => trim(str_replace('%', ' percent', $name)),
                   ];
        $postXml = '<Campaigns><row no="1">';
        foreach ($params as $key => $value) {
            $postXml .= '<FL val="'.$key.'">'.trim($value).'</FL>';
        }

        $postXml .= '</row></Campaigns>';

        $id = $this->insertRecords($this->_type, $postXml);

        return $id;
    }

    /**
     * Sync all old Campaign data
     *
     * @return string
     */
    public function syncAllQueue()
    {
        $collections = $this->_queueFactory->create()
            ->getCollection()
            ->addFieldToFilter('type', 'Campaign')
            ->getData();

        if ($collections) {
            $syncCampaign = $this->getAllCampaign($collections);

            $all = $this->insertRecords($this->_type, $syncCampaign);

            return $all;
        }
    }

    /**
     * Get All Campaign
     *
     * @param $collections
     * @return string
     */
    public function getAllCampaign($collections)
    {
        $postStartXml = '<Campaigns>';
        $postEndXml = '</Campaigns>';

        $numberConfig = $this->_scopeConfig->getValue('zohocrm/sync/number', ScopeInterface::SCOPE_STORE);

        $number = 1;
        foreach ($collections as $collection) {
            if ($number <= $numberConfig) {
                $model   = $this->_rule->load($collection['entity_id']);
                $name    = $model->getName();
                $params  = $this->_data->getCampaign($model, $this->_type);
                $params += [
                    'Campaign Name' => trim(str_replace('%', ' percent', $name)),
                ];
                $postXml = '<row no="'.$number.'">';
                foreach ($params as $key => $value) {
                    $postXml .= '<FL val="'.$key.'">'.trim($value).'</FL>';
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
