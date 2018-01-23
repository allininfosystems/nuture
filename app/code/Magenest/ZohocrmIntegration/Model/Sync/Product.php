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

use Magenest\ZohocrmIntegration\Model\Connector;
use Magenest\ZohocrmIntegration\Model\Data;
use Magenest\ZohocrmIntegration\Model\ReportFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Config\Model\ResourceModel\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\HTTP\ZendClientFactory;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Product using to sync to Products table
 *
 * @package Magenest\ZohocrmIntegration\Model\Sync
 */
class Product extends Connector
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;
    /**
     * @var QueueFactory
     */
    protected $_queueFactory;
    
    
    protected $_logger;
    /**
     * Constructor
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param Config $resourceConfig
     * @param ZendClientFactory $httpClientFactory
     * @param Data $data
     * @param ProductFactory $product
     * @param ReportFactory $reportFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        Config $resourceConfig,
        ZendClientFactory $httpClientFactory,
        Data $data,
        ProductFactory $product,
        QueueFactory $queueFactory,
        ReportFactory $reportFactory
    ) {
        parent::__construct($scopeConfig, $resourceConfig, $httpClientFactory, $reportFactory);
        $this->_type = 'Products';
        $this->_logger = $logger;
        $this->_table = 'product';
        $this->_data = $data;
        $this->_product = $product;
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
        $product = $this->_product->create();
        /** @var \Magento\Catalog\Model\Product $model */
        $model = $product->load($id);
        $name = $model->getName();
        $code = $model->getSku();
        $status = $model->getStatus();

        $params = $this->_data->getProduct($model, $this->_type);
        $params += [
            'Product Name' => $name,
            'Product Code' => $code,
            'Product Active' => $status == 1 ? true : false,
        ];
        
        if (isset($params['Description'])) {
            $strip = strip_tags($params['Description']);
            $stripReplace = str_replace('', '', $strip);
            $params['Description'] = $stripReplace;
        }

        if ($params['Product Active']) {
            $params['Product Active'] = 'true';
        }

        $postXml = '<Products><row no="1">';
        foreach ($params as $key => $value) {
            $postXml .= '<FL val="' . $key . '">' . trim($value) . '</FL>';
        }

        $postXml .= '</row></Products>';

        $id = $this->insertRecords($this->_type, $postXml);

        return $id;
    }

    /**
     * Delete Record
     *
     * @param array $data
     */
    public function delete($data)
    {
        $id = $this->searchRecords($this->_type, $data);
        if ($id) {
            $this->deleteRecords($this->_type, $id);
        }

        return;
    }

    /**
     * Sync all old product data
     *
     * @return string
     */
    public function syncAllQueue()
    {
        $collections = $this->_queueFactory->create()
            ->getCollection()
            ->addFieldToFilter('type', 'Product')
            ->getData();

        if ($collections) {
            $insertRecord = $this->allProduct($collections);
            $id = $this->insertRecords($this->_type, $insertRecord);

            return $id;
        }
    }

    /**
     * Get all record Product
     *
     * @param $collections
     * @return string
     */
    public function allProduct($collections)
    {

        $postStartXml = '<Products>';
        $postEndXml = '</Products>';
        
        $numberConfig = $this->_scopeConfig->getValue('zohocrm/sync/number', ScopeInterface::SCOPE_STORE);
        
        $number = 1;
        foreach ($collections as $collection) {
            if ($number <= $numberConfig) {
                $product = $this->_product->create();
                /** @var \Magento\Catalog\Model\Product $model */
                $model = $product->load($collection['entity_id']);
                $name = $model->getName();
                $code = $model->getSku();
                $status = $model->getStatus();

                $params = $this->_data->getProduct($model, $this->_type);
                $params += [
                    'Product Name' => $name,
                    'Product Code' => $code,
                    'Product Active' => $status == 1 ? true : false,
                ];

                if ($params['Description'] != null) {
                    $strip = strip_tags($params['Description']);
                    $params['Description'] = html_entity_decode($strip);
                }

                if ($params['Product Active']) {
                    $params['Product Active'] = 'true';
                }
                
                $postXml = '<row no="'.$number.'">';
                foreach ($params as $key => $value) {
                    $postXml .= '<FL val="' . $key . '">' . trim($value) . '</FL>';
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
