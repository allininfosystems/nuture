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
use Magento\Sales\Model\Order\Invoice as InvoiceModel;
use Magento\Catalog\Model\Product as ProductModel;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Invoice using to sync to Invoices table
 *
 * @package Magenest\ZohocrmIntegration\Model\Sync
 */
class Invoice extends Connector
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Sales\Model\Order\Invoice
     */
    protected $_invoice;

    /**
     * @var Product
     */
    protected $syncProduct;

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
     * @param ProductModel $product
     * @param InvoiceModel $invoice
     * @param Product $syncProduct
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ResourceConfig $resourceConfig,
        ZendClientFactory $httpClientFactory,
        Data $data,
        ReportFactory $reportFactory,
        ProductModel $product,
        InvoiceModel $invoice,
        QueueFactory $queueFactory,
        Product $syncProduct
    ) {
        parent::__construct($scopeConfig, $resourceConfig, $httpClientFactory, $reportFactory);
        $this->_type       = 'Invoices';
        $this->_table      = 'invoice';
        $this->_data       = $data;
        $this->_invoice    = $invoice;
        $this->_product    = $product;
        $this->syncProduct = $syncProduct;
        $this->_queueFactory = $queueFactory;
    }

    /**
     * Sync Invoice to Invoice
     *
     * @param  $id
     * @return string
     */
    public function sync($id)
    {
        $model       = $this->_invoice->load($id);
        $incrementId = $model->getIncrementId();
        $params      = $this->_data->getInvoice($model, $this->_type);
        $params     += [
                        'Subject'      => $incrementId,
                        'Status'       => 'Created',
                        'Account Name' => $model->getOrder()->getCustomerEmail(),
                       ];

        $postXml = '<Invoices><row no="1">';
        foreach ($params as $key => $value) {
            $postXml .= '<FL val="'.$key.'">'.trim($value).'</FL>';
        }

        $postXml .= '<FL val="Sales Order">'.$model->getOrder()->getIncrementId().'</FL>';
        $postXml .= $this->addProduct($model);
        $postXml .= '</row></Invoices>';

        $id = $this->insertRecords($this->_type, $postXml);

        return $id;
    }

    /**
     * Add Product Lines to Invoice
     *
     * @param  \Magento\Sales\Model\Order\Invoice $model
     * @return string
     */
    public function addProduct($model)
    {
        $i = 1;
        $productDetailXml = '<FL val="Product Details">';

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($model->getAllItems() as $item) {
            $productId = $item->getProductId();
            if (!$productId) {
                continue;
            }
            $name     = $this->_product->load($productId)->getName();
            $price    = $item->getPrice();
            $qty      = $item->getQty();
            $tax      = $item->getTaxAmount();
            $total    = $item->getRowTotal();
            $discount = $item->getDiscountAmount();
            $id       = $this->syncProduct->sync($productId);
            $productDetailXml .= '<product no="'.$i.'">';
            $productDetailXml .= '<FL val="Product Id">'.$id.'</FL>';
            $productDetailXml .= '<FL val="Product Name">'.trim($name).'</FL>';
            $productDetailXml .= '<FL val="Quantity">'.$qty.'</FL>';
            $productDetailXml .= '<FL val="List Price">'.$price.'</FL>';
            $productDetailXml .= '<FL val="Unit Price">'.$price.'</FL>';
            $productDetailXml .= '<FL val="Total">'.$total.'</FL>';
            $productDetailXml .= '<FL val="Discount">'.$discount.'</FL>';

            $total_after_discount = ($total - $discount);
            $net_total            = ($total_after_discount + $tax);

            $productDetailXml .= '<FL val="Total After Discount">'.$total_after_discount.'</FL>';
            $productDetailXml .= '<FL val="Tax">'.$tax.'</FL>';
            $productDetailXml .= '<FL val="Net Total">'.$net_total.'</FL>';
            $productDetailXml .= '</product>';
            $i++;
        }

        $productDetailXml .= '</FL>';

        $productDetailXml .= '<FL val="Sub Total">'.$model->getSubtotal().'</FL>';
        $productDetailXml .= '<FL val="Discount">'.$model->getDiscountAmount().'</FL>';
        $productDetailXml .= '<FL val="Tax">'.$model->getTaxAmount().'</FL>';
        $productDetailXml .= '<FL val="Adjustment">'.$model->getShippingAmount().'</FL>';
        $productDetailXml .= '<FL val="Grand Total">'.$model->getGrandTotal().'</FL>';

        return $productDetailXml;
    }

    /**
     * Sync all
     *
     * @return string
     */
    public function syncAllQueue()
    {
        $collections = $this->_queueFactory->create()
            ->getCollection()
            ->addFieldToFilter('type', 'Invoice')
            ->getData();

        if ($collections) {
            $syncOrders = $this->getAllInvoice($collections);

            $all       = $this->insertRecords($this->_type, $syncOrders);

            return $all;
        }
    }

    /**
     * Get All Invoice
     *
     * @param $collections
     * @return string
     */
    public function getAllInvoice($collections)
    {
        $postStartXml = '<Invoices>';
        $postEndXml = '</Invoices>';

        $numberConfig = $this->_scopeConfig->getValue('zohocrm/sync/number', ScopeInterface::SCOPE_STORE);

        $number = 1;
        foreach ($collections as $collection) {
            if ($number <= $numberConfig) {
                $model       = $this->_invoice->load($collection['entity_id']);
                $incrementId = $model->getIncrementId();
                $params      = $this->_data->getInvoice($model, $this->_type);
                $params     += [
                    'Subject'      => $incrementId,
                    'Status'       => 'Created',
                    'Account Name' => $model->getOrder()->getCustomerEmail(),
                ];

                $postXml = '<row no="'.$number.'">';
                foreach ($params as $key => $value) {
                    $postXml .= '<FL val="'.$key.'">'.trim($value).'</FL>';
                }

                $postXml .= '<FL val="Sales Order">'.$model->getOrder()->getIncrementId().'</FL>';
                $postXml .= $this->addProduct($model);
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
