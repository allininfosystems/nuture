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
 * Rewardpoints All Transactions
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block\Account;

class Transactions extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magestore\Rewardpoints\Model\ResourceModel\Transaction\Collection
     */
    protected $_transactionCollection;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_modelSession;

    /**
     * Transactions constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magestore\Rewardpoints\Model\ResourceModel\Transaction\Collection $transactionCollection
     * @param \Magento\Customer\Model\Session $modelSession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Rewardpoints\Model\ResourceModel\Transaction\Collection $transactionCollection,
        \Magento\Customer\Model\Session $modelSession,
        array $data)
    {
        $this->_transactionCollection = $transactionCollection;
        $this->_modelSession = $modelSession;
        parent::__construct($context, $data);
    }

    protected function _construct() {
        parent::_construct();
        $customerId = $this->_modelSession->getCustomerId();
        $collection = $this->_transactionCollection
                ->addFieldToFilter('customer_id', $customerId)
                ->setOrder('created_time', 'DESC')
                ->setOrder('transaction_id','DESC');
        $this->setCollection($collection);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareLayout() {
        parent::_prepareLayout();
        $pager = $this->getLayout()->createBlock('Magento\Theme\Block\Html\Pager', 'transactions_pager')
                ->setCollection($this->getCollection());
        $this->setChild('transactions_pager', $pager);
        return $this;
    }

    public function getPagerHtml() {
        return $this->getChildHtml('transactions_pager');

    }


}
