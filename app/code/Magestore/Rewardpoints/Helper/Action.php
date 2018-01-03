<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPoints Action Library Helper
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Helper;
class Action extends Config
{

    const XML_CONFIG_ACTIONS    = 'global/rewardpoints/actions';

    /**
     * reward points actions config
     *
     * @var array
     */
    protected $_config = array();

    /**
     * Actions Array (code => label)
     *
     * @var array
     */
    protected $_actions = null;


    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;


    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        parent::__construct($context);
        $actionConfig = [
            // Admin - Change by Custom
            "admin" => "\\Magestore\\Rewardpoints\\Model\\Action\\Admin",
            // Sales - Earning Actions
            "earning_invoice" => "\\Magestore\\Rewardpoints\\Model\\Action\\Earning\\Invoice",
            "earning_creditmemo" => "\\Magestore\\Rewardpoints\\Model\\Action\\Earning\\Creditmemo",
            "earning_cancel" => "\\Magestore\\Rewardpoints\\Model\\Action\\Earning\\Cancel",
            // Sales - Spending Actions
            "spending_order" => "\\Magestore\\Rewardpoints\\Model\\Action\\Spending\\Order",
            "spending_creditmemo" => "\\Magestore\\Rewardpoints\\Model\\Action\\Spending\\Creditmemo",
            "spending_cancel" => "\\Magestore\\Rewardpoints\\Model\\Action\\Spending\\Cancel",
        ];
        $this->_eventManager->dispatch(
            'action_config_rewardpoints',
            ['object'=>$this]
        );
        foreach ($actionConfig as $code => $model) {
            $this->_config[$code] = (string)$model;
        }
        $this->messageManager = $messageManager;
    }
    
    /**
     * add action config
     * 
     * @param array $config;
     * @return $this
     */
    public function setActionConfig($configs = array()){
        foreach ($configs as $code => $model) {
            $this->_config[$code] = (string)$model;
        }
        return $this;
    }
    
    /**
     * Add transaction that change customer reward point balance
     *
     * @param string $actionCode
     * @param Mage_Customer_Model_Customer $customer
     * @param type $object
     * @param array $extraContent
     * @return Magestore_RewardPoints_Model_Transaction
     */
    public function addTransaction($actionCode, $customer, $object = null, $extraContent = array())
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storeManager = $objectManager->create('Magento\Store\Model\StoreManagerInterface');
//        \Varien_Profiler::start('REWARDPOINTS_HELPER_ACTION::addTransaction');
        if (!$customer->getId()) {
            $this->messageManager->addError(
                __('Customer must be existed.')
            );

        }
        $actionModel = $this->getActionModel($actionCode);
        /** @var $actionModel Magestore_RewardPoints_Model_Action_Interface */
        $actionModel->setData(array(
            'customer'      => $customer,
            'action_object' => $object,
            'extra_content' => $extraContent
        ))->prepareTransaction();

        $transaction = $objectManager->create('Magestore\Rewardpoints\Model\Transaction');
        if (is_array($actionModel->getData('transaction_data'))) {
            $transaction->setData($actionModel->getData('transaction_data'));
        }
        $transaction->setData('point_amount', (int)$actionModel->getPointAmount());

        if (!$transaction->hasData('store_id')) {
            $transaction->setData('store_id', $storeManager->getStore()->getId());
        }

        $transaction->createTransaction(array(
            'customer_id'   => $customer->getId(),
            'customer'      => $customer,
            'customer_email'=> $customer->getEmail(),
            'title'         => $actionModel->getTitle(),
            'action'        => $actionCode,
            'action_type'   => $actionModel->getActionType(),
            'created_time'  => date('Y-m-d H:i:s'),
            'updated_time'  => date('Y-m-d H:i:s'),
        ));

//        Varien_Profiler::stop('REWARDPOINTS_HELPER_ACTION::addTransaction');
        return $transaction;
    }

    /**
     * get Class Model for Action by code
     *
     * @param string $code
     * @return string
     * @throws Exception
     */
    public function getActionModelClass($code) {
        if (isset($this->_config[$code]) && $this->_config[$code]) {
            return $this->_config[$code];
        }
        $this->messageManager->addError(
            __('Action code %1 not found on config.', $code)
        );
    }

    /**
     * get action Model by Code
     *
     * @param string $code
     * @return Magestore_RewardPoints_Model_Action_Interface
     * @throws Exception
     */
    public function getActionModel($code) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $modelClass = $this->getActionModelClass($code);
        $model = $objectManager->create($modelClass);
        if (!$model->getCode()) {
            $model->setCode($code);
        }
        return $model;
    }

    /**
     * get actions hash options
     *
     * @return array
     */
    public function getActionsHash() {
        if (is_null($this->_actions)) {
            $this->_actions = array();
            foreach ($this->_config as $code => $class) {
                try {
                    $model = $this->getActionModel($code);
                    $this->_actions[$code] = $model->getActionLabel();
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                }
            }
        }
        return $this->_actions;
    }

    /**
     * get actions array options
     *
     * @return array
     */
    public function getActionsArray() {
        $actions = array();
        foreach ($this->getActionsHash() as $value => $label) {
            $actions[] = array(
                'value' => $value,
                'label' => $label
            );
        }
        return $actions;
    }
}
