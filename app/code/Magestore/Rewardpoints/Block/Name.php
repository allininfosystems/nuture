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
 * RewardPoints Name and Image Block
 * 
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
namespace Magestore\Rewardpoints\Block;
class Name extends \Magento\Framework\View\Element\Template
{
    public $_objectManager;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context)
    {
        parent::__construct($context, []);
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->setTemplate('Magestore_Rewardpoints::rewardpoints/name.phtml');

    }

    protected function _toHtml()
    {
        return parent::_toHtml();
    }


    /**
     * get current balance of customer as text
     * 
     * @return string
     */
    public function getBalanceText() {
        return $this->_objectManager->create('Magestore\Rewardpoints\Helper\Customer')->getBalanceFormated();
    }

    /**
     * get Image (Logo) HTML for reward points
     * 
     * @return string
     */
    public function getImageHtml() {
        return $this->_objectManager->create('Magestore\Rewardpoints\Helper\Point')->getImageHtml($this->getIsAnchorMode());
    }
}
