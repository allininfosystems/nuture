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
 * RewardPoints show on customer account dashboard Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block;

class Account extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magestore\Rewardpoints\Helper\Policy
     */
    protected $helper;

    /**
     * @var \Magento\Framework\App\ObjectManager
     */
    public $_objectManager;

    /**
     * @var \Magestore\Rewardpoints\Helper\Customer
     */
    protected $_helperCustomer;

    /**
     * @var \Magestore\Rewardpoints\Helper\Point
     */
    protected $_helperPoint;

    /**
     * @var \Magestore\Rewardpoints\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magestore\Rewardpoints\Model\Rate
     */
    protected $_modelRate;

    /**
     * Account constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magestore\Rewardpoints\Helper\Policy $globalConfig
     * @param \Magestore\Rewardpoints\Helper\Customer $helperCustomer
     * @param \Magestore\Rewardpoints\Helper\Point $helperPoint
     * @param \Magestore\Rewardpoints\Helper\Data $helperData
     * @param \Magestore\Rewardpoints\Model\Rate $modelRate
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magestore\Rewardpoints\Helper\Policy $globalConfig,
        \Magestore\Rewardpoints\Helper\Customer $helperCustomer,
        \Magestore\Rewardpoints\Helper\Point $helperPoint,
        \Magestore\Rewardpoints\Helper\Data $helperData,
        \Magestore\Rewardpoints\Model\Rate $modelRate
    )
    {

        parent::__construct($context, []);
        $this->helper = $globalConfig;
        $this->_helperCustomer = $helperCustomer;
        $this->_helperPoint = $helperPoint;
        $this->_helperData = $helperData;
        $this->_modelRate = $modelRate;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();

    }

    /**
     * get current balance of customer as text
     * 
     * @return string
     */
    public function getBalanceText()
    {
        return $this->_helperCustomer->getBalanceFormated();
    }
    
    /**
     * get holding balance of customer as text
     * 
     * @return int
     */
    public function getHoldingBalance()
    {
        $holdingBalance = $this->_helperCustomer->getAccount()->getHoldingBalance();
        if ($holdingBalance > 0) {
            return $this->_helperPoint->format($holdingBalance);
        }
        return '';
    }
    
    /**
     * get point money balance of customer
     * 
     * @return string
     */
    public function getPointMoney()
    {
        $pointAmount = $this->_helperCustomer->getBalance();
        if ($pointAmount > 0) {
            $rate = $this->_modelRate->getRate(\Magestore\Rewardpoints\Model\Rate::POINT_TO_MONEY);
            if ($rate && $rate->getId()) {
                $baseAmount = $pointAmount * $rate->getMoney() / $rate->getPoints();
                return $this->_helperData->convertAndFormat($baseAmount, true);
            }
        }
        return '';
    }

    public function getImageHtml($hasAnchor = false){
        return $this->_helperPoint->getImageHtml($hasAnchor);
    }
}
