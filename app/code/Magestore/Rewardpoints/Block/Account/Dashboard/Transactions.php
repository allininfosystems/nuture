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
 * Rewardpoints Account Dashboard Recent Transactions
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block\Account\Dashboard;

class Transactions extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    public $_customerSession;

    /**
     * @var \Magestore\Rewardpoints\Model\TransactionFactory
     */
    public $_transactionFactory;

    /**
     * Transactions constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magestore\Rewardpoints\Model\TransactionFactory $transactionFactory
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magestore\Rewardpoints\Model\TransactionFactory $transactionFactory
    )
    {
        $this->_customerSession = $customerSession;
        $this->_transactionFactory = $transactionFactory;
        parent::__construct($context, []);
    }

    protected function _construct()
    {
        parent::_construct();
        $customerId = $this->_customerSession->getCustomerId();
        $collection = $this->_transactionFactory->create()->getCollection()
            ->addFieldToFilter('customer_id', $customerId);
        $collection->getSelect()->limit(5)
            ->order('created_time DESC');
        $collection->setOrder('transaction_id','DESC');
        $this->setCollection($collection);
    }
}
