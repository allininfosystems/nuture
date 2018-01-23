<?php
namespace Magenest\ZohocrmIntegration\Model;

use Magenest\ZohocrmIntegration\Model\Sync;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Cron
 * @package Magenest\ZohocrmIntegration\Model
 */
class Cron
{
    /**
     * @var Sync\Contact
     */
    protected $_contact;

    /**
     * @var Sync\Lead
     */
    protected $_lead;

    /**
     * @var Sync\Account
     */
    protected $_account;

    /**
     * @var Sync\SalesOrder
     */
    protected $_order;

    /**
     * @var Sync\Invoice
     */
    protected $_invoice;
    
    /**
     * @var Sync\Product
     */
    protected $_product;

    /**
     * @var Sync\Campaign
     */
    protected $_campaign;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var DateTime
     */
    protected $dateModel;


    /**
     * Cron constructor.
     * @param Sync\Contact $contact
     * @param Sync\Campaign $campaign
     * @param Sync\Account $account
     * @param Sync\Lead $lead
     * @param Sync\SalesOrder $order
     * @param Sync\Invoice $invoice
     * @param Sync\Product $product
     * @param ScopeConfigInterface $scopeConfig
     * @param DateTime $dateModel
     */
    public function __construct(
        Sync\Contact $contact,
        Sync\Campaign $campaign,
        Sync\Account $account,
        Sync\Lead $lead,
        Sync\SalesOrder $order,
        Sync\Invoice $invoice,
        Sync\Product $product,
        ScopeConfigInterface $scopeConfig,
        DateTime $dateModel
    ) {
        $this->_account = $account;
        $this->_contact = $contact;
        $this->_campaign = $campaign;
        $this->_lead = $lead;
        $this->_order = $order;
        $this->_invoice = $invoice;
        $this->_product = $product;
        $this->scopeConfig = $scopeConfig;
        $this->dateModel = $dateModel;
    }

    /**
     * Get Config Value
     *
     * @param $type
     * @return mixed
     */
    protected function getConfigValue($type)
    {
        $path = 'zohocrm/sync/'. $type .'_time';

        return $this->scopeConfig->getValue($path, ScopeConfigInterface::SCOPE_TYPE_DEFAULT);
    }

    /**
     * sync all queued data to ZohocrmIntegration
     * maximum 250 items at a time
     */
    public function syncData()
    {
        if ($time = $this->getConfigValue('contact')) {
            if ($time !=0 && $this->calculateTime($time)) {
                $this->_contact->syncAllQueue();
            }
        }

        if ($time = $this->getConfigValue('lead')) {
            if ($time !=0 && $this->calculateTime($time)) {
                $this->_lead->syncAllQueue();
            }
        }

        if ($time = $this->getConfigValue('account')) {
            if ($time !=0 && $this->calculateTime($time)) {
                $this->_account->syncAllQueue();
            }
        }

        if ($time = $this->getConfigValue('product')) {
            if ($time !=0 && $this->calculateTime($time)) {
                $this->_product->syncAllQueue();
            }
        }

        if ($time = $this->getConfigValue('order')) {
            if ($time !=0 && $this->calculateTime($time)) {
                $this->_order->syncAllQueue();
            }
        }

        if ($time = $this->getConfigValue('invoice')) {
            if ($time !=0 && $this->calculateTime($time)) {
                $this->_invoice->syncAllQueue();
            }
        }

        if ($time = $this->getConfigValue('campaign')) {
            if ($this->calculateTime($time)) {
                $this->_campaign->syncAllQueue();
            }
        }
    }

    /**
     * Calculate time
     *
     * @param $time
     * @return bool
     */
    protected function calculateTime($time)
    {
        $minute = date('i');
        $hour = date('h');
        /** change minute 0 to minute 60th */
        if ($minute == 0) {
            $minute = 60;
        }

        return ($minute % $time == 0) || ($time == 120 && $hour % 2 == 0);
    }
}
