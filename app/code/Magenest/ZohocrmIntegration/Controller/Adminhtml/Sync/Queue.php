<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync;

use Magenest\ZohocrmIntegration\Model\Sync;
use Magento\Framework\App\Config\ScopeConfigInterface as ScopeConfigInterface;
use Magento\Config\Model\ResourceModel\Config as ResourceModelConfig;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magenest\ZohocrmIntegration\Model\Connector;
use Magento\Framework\App\Cache\TypeListInterface;

class Queue extends \Magento\Backend\App\Action
{
    /**
     * @var Sync\Contact
     */
    protected $_contact;

    /**
     * @var Sync\Campaign
     */
    protected $_campaign;

    /**
     * @var Sync\Account
     */
    protected $_account;

    /**
     * @var Sync\Lead
     */
    protected $_lead;

    /**
     * @var Sync\SalesOrder
     */
    protected $_order;

    /**
     * @var Sync\Product
     */
    protected $_product;

    protected $_invoice;
    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var ResourceModelConfig
     */
    protected $_resourceConfig;

    /**
     * @var TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * Queue constructor.
     * @param Context $context
     * @param Sync\Contact $contact
     * @param Sync\Campaign $campaign
     * @param Sync\Account $account
     * @param Sync\Lead $lead
     * @param Sync\SalesOrder $order
     * @param Sync\Product $product
     * @param ScopeConfigInterface $scopeConfig
     * @param ResourceModelConfig $resourceConfig
     * @param TypeListInterface $cacheTypeList
     */
    public function __construct(
        Context $context,
        Sync\Contact $contact,
        Sync\Campaign $campaign,
        Sync\Account $account,
        Sync\Lead $lead,
        Sync\SalesOrder $order,
        Sync\Product $product,
        Sync\Invoice $invoice,
        ScopeConfigInterface $scopeConfig,
        ResourceModelConfig $resourceConfig,
        TypeListInterface $cacheTypeList
    ) {
        parent::__construct($context);
        $this->_contact = $contact;
        $this->_campaign = $campaign;
        $this->_account = $account;
        $this->_lead = $lead;
        $this->_order = $order;
        $this->_product = $product;
        $this->_invoice = $invoice;
        $this->_scopeConfig    = $scopeConfig;
        $this->_resourceConfig = $resourceConfig;
        $this->_cacheTypeList = $cacheTypeList;
    }

    public function execute()
    {
        $response = 'empty';
        try {
            /** Turn off auto sync due to saving custom attributes */
            $autoSyncValues = $this->getAutoSyncValues();
            $this->setAutoSyncValues([]);
            /** refresh config cache */
            $this->_cacheTypeList->cleanType('config');

            /** Sync Queue */
            $response = $this->_account->syncAllQueue();

            $response += $this->_lead->syncAllQueue();

            $response += $this->_contact->syncAllQueue();

            $response += $this->_campaign->syncAllQueue();

            $response += $this->_product->syncAllQueue();

            $response += $this->_order->syncAllQueue();

            $response += $this->_invoice->syncAllQueue();
            
            /** Reset auto sync settings */
            $this->setAutoSyncValues($autoSyncValues);
            /** refresh config cache */
            $this->_cacheTypeList->cleanType('config');

            $this->messageManager->addSuccess(
                __('All items in queue are synced')
            );
        } catch (\Exception $e) {
            $this->messageManager->addError(
                __('Something happen during syncing process. Detail: ' . $e->getMessage() . '. Response Log: '.serialize($response))
            );
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    protected function getAutoSyncValues()
    {
        return [
            Connector::XML_PATH_ZOHO_CONTACT_ENABLE     => $this->_scopeConfig->getValue(Connector::XML_PATH_ZOHO_CONTACT_ENABLE),
            Connector::XML_PATH_ZOHO_LEAD_ENABLE        => $this->_scopeConfig->getValue(Connector::XML_PATH_ZOHO_LEAD_ENABLE),
            Connector::XML_PATH_ZOHO_ACCOUNT_ENABLE     => $this->_scopeConfig->getValue(Connector::XML_PATH_ZOHO_ACCOUNT_ENABLE),
            Connector::XML_PATH_ZOHO_ORDER_ENABLE       => $this->_scopeConfig->getValue(Connector::XML_PATH_ZOHO_ORDER_ENABLE),
            Connector::XML_PATH_ZOHO_PRODUCT_ENABLE     => $this->_scopeConfig->getValue(Connector::XML_PATH_ZOHO_PRODUCT_ENABLE),
            Connector::XML_PATH_ZOHO_CAMPAIGN_ENABLE    => $this->_scopeConfig->getValue(Connector::XML_PATH_ZOHO_CAMPAIGN_ENABLE),
            Connector::XML_PATH_ZOHO_INVOICE_ENABLE     => $this->_scopeConfig->getValue(Connector::XML_PATH_ZOHO_INVOICE_ENABLE),
        ];
    }

    protected function setAutoSyncValues($values = [])
    {
        if (count($values) == 0) {
            $this->_resourceConfig->saveConfig(Connector::XML_PATH_ZOHO_CONTACT_ENABLE, 0, 'default', 0);
            $this->_resourceConfig->saveConfig(Connector::XML_PATH_ZOHO_LEAD_ENABLE, 0, 'default', 0);
            $this->_resourceConfig->saveConfig(Connector::XML_PATH_ZOHO_ACCOUNT_ENABLE, 0, 'default', 0);
            $this->_resourceConfig->saveConfig(Connector::XML_PATH_ZOHO_ORDER_ENABLE, 0, 'default', 0);
            $this->_resourceConfig->saveConfig(Connector::XML_PATH_ZOHO_PRODUCT_ENABLE, 0, 'default', 0);
            $this->_resourceConfig->saveConfig(Connector::XML_PATH_ZOHO_CAMPAIGN_ENABLE, 0, 'default', 0);
            $this->_resourceConfig->saveConfig(Connector::XML_PATH_ZOHO_INVOICE_ENABLE, 0, 'default', 0);
        } else {
            foreach ($values as $key => $value) {
                $this->_resourceConfig->saveConfig($key, $value, 'default', 0);
            }
        }
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ZohocrmIntegration::config_zohocrm');
    }
}
