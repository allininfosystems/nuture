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
 * Rewardpoints Settings
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block\Account;

class Settings extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $_objectManager;
    /**
     * @var \Magestore\Rewardpoints\Helper\Customer
     */
    protected $_helperCustomer;

    /**
     * Settings constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magestore\Rewardpoints\Helper\Customer $helperCustomer
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Rewardpoints\Helper\Customer $helperCustomer
    )
    {
        parent::__construct($context, []);
        $this->_helperCustomer = $helperCustomer;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * get current reward points account
     * 
     * @return Magestore_RewardPoints_Model_Customer
     */
    public function getRewardAccount()
    {
        $rewardAccount = $this->_helperCustomer->getAccount();
        if (!$rewardAccount->getId()) {
            $rewardAccount->setIsNotification(1)
                ->setExpireNotification(1);
        }
        return $rewardAccount;
    }
}
