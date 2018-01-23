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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Config\Model\ResourceModel\Config as ResourceConfig;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Store\Model\ScopeInterface;

/**
 * Connect to ZohocrmIntegration using REST API
 *
 * Class Connector
 *
 * @package Magenest\ZohocrmIntegration\Model
 */
class Connector
{

    /**
     * Configuration value
     *
     * @const
     */
    const XML_PATH_ZOHO_CONFIG_EMAIL       = 'zohocrm/config/email';
    const XML_PATH_ZOHO_CONFIG_PASSWD      = 'zohocrm/config/passwd';
    const XML_PATH_ZOHO_CONFIG_AUTHTOKEN   = 'zohocrm/config/auth_token';
    const XML_PATH_ZOHO_CONFIG_SAVE_REPORT = 'zohocrm/config/save_report';
    const XML_PATH_ZOHO_CONTACT_ENABLE     = 'zohocrm/zohocrm_sync/contact';
    const XML_PATH_ZOHO_LEAD_ENABLE        = 'zohocrm/zohocrm_sync/lead';
    const XML_PATH_ZOHO_ACCOUNT_ENABLE     = 'zohocrm/zohocrm_sync/account';
    const XML_PATH_ZOHO_ORDER_ENABLE       = 'zohocrm/zohocrm_sync/order';
    const XML_PATH_ZOHO_PRODUCT_ENABLE     = 'zohocrm/zohocrm_sync/product';
    const XML_PATH_ZOHO_CAMPAIGN_ENABLE    = 'zohocrm/zohocrm_sync/campaign';
    const XML_PATH_ZOHO_INVOICE_ENABLE     = 'zohocrm/zohocrm_sync/invoice';

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $_httpClientFactory;

    /**
     * Core Config Data
     *
     * @var $_scopeConfig \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Using save Report
     *
     * @var ReportFactory
     */
    protected $_reportFactory;

    /**
     * @var string
     */
    protected $base_url;

    /**
     * Connector constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceConfig $resourceConfig
     * @param ZendClientFactory $httpClientFactory
     * @param ReportFactory $reportFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ResourceConfig $resourceConfig,
        ZendClientFactory $httpClientFactory,
        ReportFactory $reportFactory
    ) {
        $this->_scopeConfig       = $scopeConfig;
        $this->_resourceConfig    = $resourceConfig;
        $this->_reportFactory     = $reportFactory;
        $this->base_url           = 'https://crm.zoho.com/crm/private/xml/';
        $this->_httpClientFactory = $httpClientFactory;
    }

    /**
     * Get Access Token
     *
     * @param  array      $data
     * @param  bool|false $refresh
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function getAuth($data = array(), $refresh = false)
    {
        $authkey = $this->_scopeConfig->getValue(self::XML_PATH_ZOHO_CONFIG_AUTHTOKEN, ScopeInterface::SCOPE_STORE);
        if (!$authkey || $refresh) {
            if (is_array($data) && !empty($data)) {
                $email    = $data['username'];
                $password = $data['password'];
            } else {
                $email = $this->_scopeConfig->getValue(self::XML_PATH_ZOHO_CONFIG_EMAIL, ScopeInterface::SCOPE_STORE);
                $password = $this->_scopeConfig->getValue(self::XML_PATH_ZOHO_CONFIG_PASSWD, ScopeInterface::SCOPE_STORE);
            }

            $url      = "https://accounts.zoho.com/apiauthtoken/nb/create";
            $paramter = "SCOPE=ZohoCRM/crmapi&EMAIL_ID=".$email."&PASSWORD=".$password;

            $client = $this->_httpClientFactory->create();
            $client->setUri($url);
            $client->setConfig([ 'timeout' => 300]);
            if ($paramter) {
                $client->setRawData($paramter);
            }

            $response  = $client->request('POST')->getBody();
            $anArray   = explode("\n", $response);
            $authToken = explode("=", $anArray['2']);

            $authkey = $authToken['1'];
            $this->_resourceConfig->saveConfig(self::XML_PATH_ZOHO_CONFIG_AUTHTOKEN, $authkey, 'default', 0);
        }
        return $authkey;
    }

    /**
     * Send Request to ZohoCRM Server
     *
     * @param  $path
     * @param  null   $paramter
     * @param  string $method
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function _sendRequest($path, $paramter = null, $method = \Zend_Http_Client::GET)
    {
        $authtoken = $this->getAuth();
        $url       = $this->base_url.$path;
        $params    = "authtoken=".$authtoken."&scope=crmapi";
        $paramter  = $params.$paramter;

        $client = $this->_httpClientFactory->create();
        $client->setUri($url);
        $client->setConfig([ 'timeout' => 300]);
        if ($paramter) {
            $client->setRawData($paramter);
        }

        $response = $client->request($method)->getBody();
        // Convert result format XML from Zoho to array
        $parser = xml_parser_create();
        xml_parse_into_struct($parser, $response, $result, $index);
        xml_parser_free($parser);
        try {
            if ($result[1]['tag'] == 'ERROR' || $result[1] == 'ERROR') {
                throw new \Exception("Can't sync to ZohoCRM. Because unable to parse XML data, please check mapping or data sync again !");
            }
        } catch (\Exception $exception) {
        }

        return $result;
    }

    /**
     * Get fields from a Module in ZohoCRM
     *
     * @param  string $table
     * @return string
     */
    public function getFields($table)
    {
        $path       = $table."/getFields";
        $result     = $this->_sendRequest($path, null, 'POST');
        $field_zoho = [];
        $type_array = [
                       'Multiselect Pick List',
                       'Lookup',
                       'Pick List',
                       'OwnerLookup',
                      ];
        foreach ($result as $key => $value) {
            if ($value['tag'] == 'FL' && !empty($value['attributes']['LABEL'])) {
                $type = $value['attributes']['TYPE'];
                if (!in_array($type, $type_array)) {
                    $label = $value['attributes']['LABEL'];
                    $field_zoho[$label] = $label.' ('.$type.')';
                    // save Zoho Field to array
                }
            }
        }

        return serialize($field_zoho);
    }

    /**
     * Create new Record in ZohoCRM
     *
     * @param  string $table
     * @param  string $postXML
     * @return string or false
     */
    public function insertRecords($table, $postXML)
    {
        $path     = $table.'/insertRecords';
        $postXML  = str_replace('%', '', str_replace('&', ' ', $postXML));
        $paramter = "&duplicateCheck=2&newFormat=1&vesion=2&xmlData=".$postXML;
        $response = $this->_sendRequest($path, $paramter, \Zend_Http_Client::POST);
        $action   = '';
        if ($response[2]['tag'] == 'MESSAGE') {
            $sub = substr($response[2]['value'], 10, 5);
            if ($sub == 'added') {
                $action = "Create";
            } else {
                $action = "Update";
            }
        }

        if ($response[4]['tag'] != 'ERROR') {
            $id = $response[4]['value'];
            $this->saveReport($id, $action, $table);
            return $id;
        }

        return false;
    }

    /**
     * Update Record in ZohoCRM
     *
     * @param string $table
     * @param string $id
     * @param string $postXML
     */
    public function updateRecords($table, $id, $postXML)
    {
        $path     = $table.'/updateRecords';
        $postXML  = str_replace('&', ' ', $postXML);
        $paramter = "&id=$id&newFormat=1&xmlData={$postXML}";
        $this->_sendRequest($path, $paramter, \Zend_Http_Client::POST);
        return;
    }

    /**
     * Delete a Record
     *
     * @param $table
     * @param $id
     */
    public function deleteRecords($table, $id)
    {
        $path     = $table.'/deleteRecords';
        $paramter = "&id=$id";
        $this->_sendRequest($path, $paramter, \Zend_Http_Client::POST);
        $this->saveReport($id, 'Delete', $table);
        return;
    }

    /**
     * Search recordId in Zoho
     *
     * @param  string       $table
     * @param  string array $data
     * @return string or false
     */
    public function searchRecords($table, $data)
    {
        $path   = $table."/searchRecords";
        $params = "&criteria=";
        if ($table == 'Products') {
            $params .= "(Product Code:".$data.")";
        } elseif ($table == 'Accounts') {
            $params .= "(Account Name:$data)";
        } elseif ($table == 'SalesOrders') {
            $params .= "(Subject:".$data.")";
        } else {
            $params .= "(Email:$data)";
        }

        $response = $this->_sendRequest($path, $params, 'POST');
        if ($response[1]['tag'] == 'RESULT') {
            $id = $response[4]['value'];
            return $id;
        } else {
            return false;
        }
    }

    /**
     * Save Report: History sync
     *
     * @param $id
     * @param $action
     * @param $table
     */
    public function saveReport($id, $action, $table)
    {
        if (!$this->_scopeConfig->isSetFlag(self::XML_PATH_ZOHO_CONFIG_SAVE_REPORT, ScopeInterface::SCOPE_STORE)) {
            return;
        }

        $model = $this->_reportFactory->create();
        $model->saveReport($id, $action, $table);
        return;
    }
}
