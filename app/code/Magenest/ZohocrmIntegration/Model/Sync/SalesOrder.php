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
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Framework\HTTP\ZendClientFactory;
use Magenest\ZohocrmIntegration\Model\Data;
use Magento\Catalog\Model\ProductFactory;
use Magenest\ZohocrmIntegration\Model\ReportFactory;
use Magento\Sales\Model\OrderFactory;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Class SalesOrder using to sync SalesOrder
 *
 * @package Magenest\ZohocrmIntegration\Model\Sync
 */
class SalesOrder extends Connector
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @var Account
     */
    protected $_account;

    /**
     * @var Contact
     */
    protected $_contact;

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
     * @param ProductFactory $product
     * @param \Magento\Sales\Model\OrderFactory $order
     * @param Account $account
     * @param Contact $contact
     * @param Product $syncProduct
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ResourceConfig $resourceConfig,
        ZendClientFactory $httpClientFactory,
        Data $data,
        ReportFactory $reportFactory,
        ProductFactory $product,
        OrderFactory $order,
        QueueFactory $queueFactory,
        Account $account,
        Contact $contact,
        Product $syncProduct
    ) {
        parent::__construct($scopeConfig, $resourceConfig, $httpClientFactory, $reportFactory);
        $this->_type       = 'SalesOrders';
        $this->_table      = 'order';
        $this->_data       = $data;
        $this->_order      = $order;
        $this->_account    = $account;
        $this->_contact    = $contact;
        $this->_product    = $product;
        $this->syncProduct = $syncProduct;
        $this->_queueFactory = $queueFactory;
    }

    /**
     * Sync Orders to SalesOrders
     *
     * @param  $id
     * @return string
     */
    public function sync($id)
    {
        $model      = $this->_order->create()->loadByIncrementId($id);
        $customerId = $model->getCustomerId();
        $email      = $model->getCustomerEmail();

        // Sync Account and Contact
        if ($customerId) {
            $this->_account->sync($customerId);
            $this->_contact->sync($customerId);
        } else {
            $data = [
                     'Email'      => $email,
                     'First Name' => $model->getCustomerFirstname(),
                     'Last Name'  => $model->getCustomerLastname(),
                    ];
            $this->_account->syncByEmail($email);
            $this->_contact->syncByEmail($data);
        }

        $params  = $this->_data->getOrder($model, $this->_type);
        $params += [
                    'Subject'      => $model->getIncrementId(),
                    'Account Name' => $email,
                    'Status'       => 'Created',
                   ];

        $postXml = '<SalesOrders><row no="1">';

        foreach ($params as $key => $value) {
            $postXml .= '<FL val="'.$key.'">'.trim($value).'</FL>';
        }

        $postXml .= $this->addProduct($model);
        $postXml .= '</row></SalesOrders>';
        $id       = $this->insertRecords($this->_type, $postXml);

        return $id;
    }

    /**
     * Add Product Lines to Sales Orders
     *
     * @param  \Magento\Sales\Model\Order $model
     * @return string
     */
    public function addProduct($model)
    {

        $i = 1;
        $productDetailXml = '<FL val="Product Details">';
        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($model->getAllItems() as $item) {
            $productId = $item->getProductId();

            $name     = $this->_product->create()->load($productId)->getName();
            $price    = $item->getPrice();
            $qty      = $item->getQtyOrdered();
            $tax      = $item->getTaxAmount();
            $total    = $item->getRowTotal();
            $discount = $item->getDiscountAmount();

            $id = $this->syncProduct->sync($productId);
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
     * Sync all old data sales order
     *
     * @return string
     */
    public function syncAllQueue()
    {
        $collections = $this->_queueFactory->create()
            ->getCollection()
            ->addFieldToFilter('type', 'Order')
            ->getData();

        if ($collections) {
            $syncOrders = $this->getAllOrder($collections);

            $all       = $this->insertRecords($this->_type, $syncOrders);

            return $all;
        }
    }

    /**
     * Get all sales order
     *
     * @param $collections
     * @return string
     */
    public function getAllOrder($collections)
    {
        $postStartXml = '<SalesOrders>';
        $postEndXml = '</SalesOrders>';

        $numberConfig = $this->_scopeConfig->getValue('zohocrm/sync/number', ScopeInterface::SCOPE_STORE);

        $number = 1;
        foreach ($collections as $collection) {
            if ($number <= $numberConfig) {
                $model      = $this->_order->create()->loadByIncrementId($collection['entity_id']);
                $customerId = $model->getCustomerId();
                $email      = $model->getCustomerEmail();

                // Sync Account and Contact
                if ($customerId) {
                    $this->_account->sync($customerId);
                    $this->_contact->sync($customerId);
                } else {
                    $data = [
                        'Email'      => $email,
                        'First Name' => $model->getCustomerFirstname(),
                        'Last Name'  => $model->getCustomerLastname(),
                    ];
                    $this->_account->syncByEmail($email);
                    $this->_contact->syncByEmail($data);
                }

                $params  = $this->_data->getOrder($model, $this->_type);
                $params += [
                    'Subject'      => $model->getIncrementId(),
                    'Account Name' => $email,
                    'Status'       => 'Created',
                ];

                $postXml = '<row no="'.$number.'">';

                foreach ($params as $key => $value) {
                    $postXml .= '<FL val="'.$key.'">'.trim($value).'</FL>';
                }

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
