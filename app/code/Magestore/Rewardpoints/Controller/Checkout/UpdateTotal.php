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
 * @package     Magestore_Giftvoucher
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
namespace Magestore\Rewardpoints\Controller\Checkout;

/**
 * @category Magestore
 * @package  Magestore_Affiliateplus
 * @module   Affiliateplus
 * @author   Magestore Developer
 */
class UpdateTotal extends \Magestore\Rewardpoints\Controller\AbstractAction
{
    /**
     * @return mixed
     */
    public function execute()
    {
        $this->_checkoutSession->setData('use_point', true);
        $this->_checkoutSession->setRewardSalesRules(array(
            'rule_id' => $this->getRequest()->getPostValue()['reward_sales_rule'],
            'use_point' => $this->getRequest()->getPostValue()['reward_sales_point'],
        ));
        if ($this->_checkoutCart->getQuote()->getItemsCount()) {
//            $cart->init();
            $this->_checkoutCart->save();
            $this->checkUseDefault();
        }
        $this->_checkoutSession->getQuote()->collectTotals()->save();
        $amount = $this->_checkoutCart->getQuote()->getRewardpointsBaseDiscount();
        $result = [
            'earning' => $this->_helperPoint->format($this->_checkoutForm->getEarningPoint()),
            'spending' => $this->_helperPoint->format($this->_checkoutForm->getSpendingPoint()),
            'usePoint' =>  strip_tags($this->_helperData->convertAndFormat(-$amount)),
        ];
        return $this->getResponse()->setBody(\Zend_Json::encode($result));

    }

    public function checkUseDefault(){
        $this->_checkoutSession->setData('use_max', 0);
        $rewardSalesRules = $this->_checkoutSession->getRewardSalesRules();
        $arrayRules = $this->_helperSpend->getRulesArray();
        if($this->_calculationSpending->isUseMaxPointsDefault()){
            if(isset($rewardSalesRules['use_point']) &&
                isset($rewardSalesRules['rule_id']) &&
                isset($arrayRules[$rewardSalesRules['rule_id']]) &&
                isset($arrayRules[$rewardSalesRules['rule_id']]['sliderOption'])&&
                isset($arrayRules[$rewardSalesRules['rule_id']]['sliderOption']['maxPoints']) && ($rewardSalesRules['use_point'] < $arrayRules[$rewardSalesRules['rule_id']]['sliderOption']['maxPoints'])){
                $this->_checkoutSession->setData('use_max', 0);
            }else{
                $this->_checkoutSession->setData('use_max', 1);
            }

           

        }
    }

}