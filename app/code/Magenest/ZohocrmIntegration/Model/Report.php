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

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Backend\Model\Auth\Session;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class Report: Save Report
 *
 * @package Magenest\ZohocrmIntegration\Model
 */
class Report extends AbstractModel
{
    /**
     * Core Date
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_coreDate;

    /**
     * Session Admin
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_backendAuthSession;

    /**
     * Session Customer
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;


    /**
     * @param Context $context
     * @param Registry $registry
     * @param DateTime $coreDate
     * @param Session $backendAuthSession
     * @param CustomerSession $customerSession
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        DateTime $coreDate,
        Session $backendAuthSession,
        CustomerSession $customerSession,
        array $data = []
    ) {
        $this->_coreDate           = $coreDate;
        $this->_backendAuthSession = $backendAuthSession;
        $this->_customerSession    = $customerSession;
        parent::__construct($context, $registry);
    }

    /**
     *
     */
    public function _construct()
    {
        $this->_init('Magenest\ZohocrmIntegration\Model\ResourceModel\Report');
        $this->setIdFieldName('id');
    }

    /**
     * Save Report
     *
     * @param $id
     * @param $action
     * @param $table
     */
    public function saveReport($id, $action, $table)
    {
        $datetime     = $this->_coreDate->gmtDate();
        $admin_user   = $this->_backendAuthSession->getUser();
        $current_user = $this->_customerSession->getCustomer();
        if ($admin_user) {
            $name  = $admin_user->getName();
            $email = $admin_user->getEmail();
        } elseif ($current_user->getName()) {
            $name  = $current_user->getName();
            $email = $current_user->getEmail();
        } else {
            $name  = "Guest";
            $email = '';
        }

        $data = [
                 'record_id'     => $id,
                 'action'        => $action,
                 'zohocrm_table' => $table,
                 'datetime'      => $datetime,
                 'username'      => $name,
                 'email'         => $email,
                 'status'        => 1,
                ];
        $this->setData($data);
        $this->save();
        return;
    }
}
