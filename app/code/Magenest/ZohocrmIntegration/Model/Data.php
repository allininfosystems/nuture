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
namespace Magenest\ZohocrmIntegration\Model;

use Magento\Directory\Model\Country;
use \Magento\Tax\Model\ClassModel as TaxClassModel;
use Magento\Customer\Model\Customer;

/**
 * Data Model get Data from Object in Magento
 *
 * @author Thao Pham <thaophamit@gmail.com>
 */
class Data
{
    /**
     * Setup in Configuration
     *
     * @const
     */
    const XML_PATH_ALLOW_SYNC_LEAD     = 'zohocrm/zohocrm_sync/lead';
    const XML_PATH_ALLOW_SYNC_ACCOUNT  = 'zohocrm/zohocrm_sync/account';
    const XML_PATH_ALLOW_SYNC_CONTACT  = 'zohocrm/zohocrm_sync/contact';
    const XML_PATH_ALLOW_SYNC_ORDER    = 'zohocrm/zohocrm_sync/order';
    const XML_PATH_ALLOW_SYNC_INVOICE  = 'zohocrm/zohocrm_sync/invoice';
    const XML_PATH_ALLOW_SYNC_CAMPAIGN = 'zohocrm/zohocrm_sync/campaign';
    const XML_PATH_ALLOW_SYNC_PRODUCT  = 'zohocrm/zohocrm_sync/product';

    /**
     * @var \Magenest\ZohocrmIntegration\Model\MapFactory
     */
    protected $_mapFactory;

    /**
     * @var \Magenest\ZohocrmIntegration\Model\Field
     */
    protected $_field;

    /**
     * @var \Magento\Directory\Model\Country
     */
    protected $_country;
    /**
     * @var \Magento\Directory\Model\Country
     */
    protected $_tax;

    protected $_address;

    protected $_logger;

    protected $_customer;

    protected $_addressRepository;


    /**
     * @param MapFactory                       $map
     * @param Field                            $field
     * @param \Magento\Directory\Model\Country $country
     * @param \Magento\Tax\Model\ClassModel    $tax
     */
    public function __construct(
        MapFactory $map,
        Field $field,
        Country $country,
        \Psr\Log\LoggerInterface $logger,
        Customer $customer,
        \Magento\Customer\Model\ResourceModel\AddressRepository $addressRepository,
        TaxClassModel $tax
    ) {
        $this->_customer = $customer;
        $this->_logger = $logger;
        $this->_mapFactory = $map;
        $this->_field      = $field;
        $this->_country    = $country;
        $this->_tax        = $tax;
        $this->_addressRepository = $addressRepository;
    }//end __construct()


    /**
     * Select mapping
     *
     * @param  string $data
     * @param  string $_type
     * @return array
     */
    public function getMapping($data, $_type)
    {
        $model      = $this->_mapFactory->create();
        $collection = $model->getResourceCollection()
            ->addFieldToFilter('type', $_type)
            ->addFieldToFilter('status', 1);
        $map        = [];
        $result     = [];

        foreach ($collection as $key => $value) {
            $zoho       = $value->getZohoField();
            $magento          = $value->getMagentoField();
            $map[$zoho] = $magento;
        }

        foreach ($map as $key => $value) {
            if ($data[$value]) {
                $result[$key] = $data[$value];
            }
        }

        return $result;
    }

    /**
     * Get Country Name
     *
     * @param  string $id
     * @return string
     */
    public function getCountryName($id)
    {
        $model = $this->_country->loadByCode($id);

        return $model->getName();
    }

    /**
     * Get all data of Customer
     *
     * @param  \Magento\Customer\Model\Customer $model
     * @param  string                           $_type
     * @return array
     */
    public function getCustomer($model, $_type)
    {

        $magento_fields = $this->_field->getMagentoFields('customer');
        $data           = [];
        foreach ($magento_fields as $key => $item) {
            $sub = substr($key, 0, 5);
            if ($sub == 'bill_' && $model->getDefaultBillingAddress()) {
                $value      = substr($key, 5);
                $billing    = $model->getDefaultBillingAddress();
                $data[$key] = $billing->getData($value);
            } elseif ($sub == 'ship_' && $model->getDefaultShippingAddress()) {
                $value      = substr($key, 5);
                $shipping   = $model->getDefaultShippingAddress();
                $data[$key] = $shipping->getData($value);
            } else {
                $data[$key] = $model->getData($key);
            }
        }

        if (!empty($data['bill_country_id'])) {
            $country_id = $data['bill_country_id'];
            $data['bill_country_id'] = $this->getCountryName($country_id);
        }

        if (!empty($data['ship_country_id'])) {
            $country_id = $data['ship_country_id'];
            $data['ship_country_id'] = $this->getCountryName($country_id);
        }

        // Mapping data
        $params = $this->getMapping($data, $_type);

        return $params;
    }

    public function getCustomerDefaultBillingAddress($customer)
    {
        $defaultBillingId = (int)$customer->getData('default_billing');
        if ($defaultBillingId > 0) {
            $address = $this->_addressRepository->getById($defaultBillingId);
            return $address;
        }
        return false;
    }
    public function getCustomerDefaultShippingAddress($customer)
    {
        $defaultBillingId = (int) $customer->getData('default_shipping');
        if ($defaultBillingId > 0) {
            $address = $this->_addressRepository->getById($defaultBillingId);
            return $address;
        }
        return false;
    }

    public function getFullCustomer($id, $type)
    {
        $data = array();

        $this->_logger->debug($id);
        $this->_customer->unsetData();
        $this->_customer->getAddressesCollection()->clear();

        $model = $this->_customer->load($id);

        $magento_fields = $this->_field->getMagentoFields('customer');

        foreach ($magento_fields as $key => $item) {
            $sub = substr($key, 0, 5);
            if ($sub == 'bill_') {
                if ($billing = $this->getCustomerDefaultBillingAddress($model)) {
                    $value = ucfirst(substr($key, 5));

//                    $this->_logger->debug(print_r($value,true));

                    if ($value != 'Country_id') {
                        if (method_exists($billing, 'get' . $value)) {
                            $billValue = call_user_func([$billing, 'get' . $value]);

                            //check street
                            if ($value == 'Street') {
                                if (isset($billValue[1])) {
                                    $data[$key] = $billValue[0] . $billValue[1];
                                } else {
                                    $data[$key] = $billValue[0];
                                }
                            } elseif (is_string($billValue)) {
                                $data[$key] = $billValue;
                            }

                            //check  Region
                            if ($value == 'Region') {
                                $region = $billValue->getRegion();
                                if ($region) {
                                    $data[$key] = $region;
                                } else {
                                    $data[$key] = '';
                                }
                            }
                        } else {
                            $data[$key] = '';
                        }
                    } else {
                        $countryId = $billing->getCountryId();
                        if ($countryId) {
                            $data[$key] = $countryId;
                        } else {
                            $data[$key] = '';
                        }
                    }
                }
            } elseif ($sub == 'ship_') {
                if ($shipping = $this->getCustomerDefaultShippingAddress($model)) {
                    $value = ucfirst(substr($key, 5));

                    if ($value != 'Country_id') {
                        if (method_exists($shipping, 'get' . $value)) {
                            $shipValue = call_user_func([$shipping, 'get' . $value]);

                            if ($value == 'Street') {
                                if (isset($shipValue[1])) {
                                    $data[$key] = $shipValue[0] . $shipValue[1];
                                } else {
                                    $data[$key] = $shipValue[0];
                                }
                            } elseif (is_string($shipValue)) {
                                $data[$key] = $shipValue;
                            }

                            if ($value == 'Region') {
                                $region = $shipValue->getRegion();
                                if ($region) {
                                    $data[$key] = $region;
                                } else {
                                    $data[$key] = '';
                                }
                            }
                        } else {
                            $data[$key] = '';
                        }
                    } else {
                        $countryId = $shipping->getCountryId();
                        if ($countryId) {
                            $data[$key] = $countryId;
                        } else {
                            $data[$key] = '';
                        }
                    }
                }
            } else {
                $data[$key] = $model->getData($key);
            }
        }

        if (!empty($data['bill_country_id'])) {
            $country_id = $data['bill_country_id'];
            $data['bill_country_id'] = $this->getCountryName($country_id);
        }

        if (!empty($data['ship_country_id'])) {
            $country_id = $data['ship_country_id'];
            $data['ship_country_id'] = $this->getCountryName($country_id);
        }

//        $this->_logger->debug(print_r($data,true));

        /* Mapping data*/
        $params = $this->getMapping($data, $type);

        return $params;
    }

    /**
     * Pass data of CatalogRule to array and return after mapping
     *
     * @param  \Magento\CatalogRule\Model\Rule $model
     * @param  string                          $_type
     * @return array
     */
    public function getCampaign($model, $_type)
    {

        $magento_fields = $this->_field->getMagentoFields('catalogrule');
        $data           = [];

        // Pass data of catalog rule price to array
        foreach ($magento_fields as $key => $item) {
            $data[$key] = $model->getData($key);
        }

        $action = [
            'by_percent' => 'By Percentage of the Original Price',
            'by_fixed'   => 'By Fixed Amount',
            'to_percent' => 'To Percentage of the Original Price',
            'to_fixed'   => 'To Fixed Amount',
        ];
        if (!empty($data['simple_action'])) {
            foreach ($action as $key => $value) {
                if ($data['simple_action'] == $key) {
                    $data['simple_action'] = $value;
                }
            }
        }

        if ($data['sub_is_enable'] == 1) {
            $data['sub_is_enable'] = 'Yes';
            foreach ($action as $key => $value) {
                if ($data['simple_action'] == $key) {
                    $data['simple_action'] = $value;
                }
            }
        } else {
            $data['sub_is_enable'] = 'No';
        }

        // Mapping data
        $params = $this->getMapping($data, $_type);

        return $params;
    }

    /**
     * Pass data of Order to array and return mapping
     *
     * @param  \Magento\Sales\Model\Order $model
     * @param  string                     $_type
     * @return array
     */
    public function getOrder($model, $_type)
    {

        $magento_fields = $this->_field->getMagentoFields('order');
        $data           = [];

        foreach ($magento_fields as $key => $item) {
            $sub = substr($key, 0, 5);
            if ($sub == 'bill_' && $model->getBillingAddress() !== null) {
                $billing    = $model->getBillingAddress();
                $data[$key] = $billing->getData(substr($key, 5));
            } elseif ($sub == 'ship_' && $model->getShippingAddress() !== null) {
                $shipping   = $model->getShippingAddress();
                $data[$key] = $shipping->getData(substr($key, 5));
            } else {
                $data[$key] = $model->getData($key);
            }
        }

        if (!empty($data['bill_country_id'])) {
            $country_id = $data['bill_country_id'];
            $data['bill_country_id'] = $this->getCountryName($country_id);
            ;
        }

        if (!empty($data['ship_country_id'])) {
            $country_id = $data['ship_country_id'];
            $data['ship_country_id'] = $this->getCountryName($country_id);
            ;
        }

        // Mapping data
        $params = $this->getMapping($data, $_type);

        return $params;
    }

    /**
     * Pass data of Product to array and return after mapping
     *
     * @param  \Magento\Catalog\Model\Product $model
     * @param  string                         $_type
     * @return array
     */
    public function getProduct($model, $_type)
    {

        $magento_fields = $this->_field->getMagentoFields('product');
        $data           = [];

        // ..........Pass data of Product to array..........
        foreach ($magento_fields as $key => $item) {
            $sub = substr($key, 0, 5);
            if ($sub == 'stock') {
                $stockItem  = $model->getExtensionAttributes()->getStockItem();
                $data[$key] = $stockItem->getData(substr($key, 6));
            } else {
                $data[$key] = $model->getData($key);
            }
        }

        if (!empty($data['country_of_manufacture'])) {
            $country_id = $data['country_of_manufacture'];
            $data['country_of_manufacture'] = $this->getCountryName($country_id);
        }

        if (!empty($data['tax_class_id'])) {
            $tax_id = $data['tax_class_id'];
            if ($tax_id == 0) {
                $data['tax_class_id'] = "None";
            } else {
                $data['tax_class_id'] = $this->_tax->load($tax_id)->getClassName();
            }
        }

        // .............End pass data...............
        // 4. Mapping data
        $params = $this->getMapping($data, $_type);

        return $params;
    }

    /**
     * Pass data of Invoice to array and return after mapping
     *
     * @param  \Magento\Sales\Model\Order\Invoice $model
     * @param  string                             $_type
     * @return array
     */
    public function getInvoice($model, $_type)
    {

        $magento_fields = $this->_field->getMagentoFields('invoice');
        $data           = [];

        foreach ($magento_fields as $key => $item) {
            $sub = substr($key, 0, 5);
            if ($sub == 'bill_' && $model->getBillingAddress() !== null) {
                $billing    = $model->getBillingAddress();
                $data[$key] = $billing->getData(substr($key, 5));
            } elseif ($sub == 'ship_' && $model->getShippingAddress() !== null) {
                $shipping   = $model->getShippingAddress();
                $data[$key] = $shipping->getData(substr($key, 5));
            } else {
                $data[$key] = $model->getData($key);
            }
        }

        $data['order_increment_id'] = $model->getOrderIncrementId();
        if (!empty($data['bill_country_id'])) {
            $country_id = $data['bill_country_id'];
            $data['bill_country_id'] = $this->getCountryName($country_id);
            ;
        }

        if (!empty($data['ship_country_id'])) {
            $country_id = $data['ship_country_id'];
            $data['ship_country_id'] = $this->getCountryName($country_id);
        }

        // Mapping data
        $params = $this->getMapping($data, $_type);

        return $params;
    }
}
