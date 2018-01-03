<?php

namespace Nuluv\Freeshipamt\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper {

	
	const CONFIG_CUSTOM_IS_ENABLED_FREESHIPAMT = 'freeshipamt_customfee/freeshipamt_customfee/freeshipamt_status';
	const CONFIG_CUSTOM_FEE_FREESHIPAMT = 'freeshipamt_customfee/freeshipamt_customfee/freeshipamt_customfeeamount';
	const CONFIG_FEE_LABEL_FREESHIPAMT = 'freeshipamt_customfee/freeshipamt_customfee/freeshipamt_name';
	const CONFIG_MINIMUM_ORDER_AMOUNT_FREESHIPAMT = 'freeshipamt_customfee/freeshipamt_customfee/freeshipamt_minimumorderamount';

	public function isModuleEnabledFreeshipamt() {
		$storeScope = \Magento\Store\Model\ScopeInterface :: SCOPE_STORE;
		$isEnabled = $this -> scopeConfig -> getValue(self :: CONFIG_CUSTOM_IS_ENABLED_FREESHIPAMT, $storeScope);
		return $isEnabled;
	} 

	public function getCustomFeeFreeshipamt() {
		$storeScope = \Magento\Store\Model\ScopeInterface :: SCOPE_STORE;
		$fee = $this -> scopeConfig -> getValue(self :: CONFIG_CUSTOM_FEE_FREESHIPAMT, $storeScope);
		return $fee;
	} 

	public function getFeeLabelFreeshipamt() {
		$storeScope = \Magento\Store\Model\ScopeInterface :: SCOPE_STORE;
		$feeLabel = $this -> scopeConfig -> getValue(self :: CONFIG_FEE_LABEL_FREESHIPAMT, $storeScope);
		return $feeLabel;
	} 

	public function getMinimumOrderAmountFreeshipamt() {
		$storeScope = \Magento\Store\Model\ScopeInterface :: SCOPE_STORE;
		$MinimumOrderAmount = $this -> scopeConfig -> getValue(self :: CONFIG_MINIMUM_ORDER_AMOUNT_FREESHIPAMT, $storeScope);
		return $MinimumOrderAmount;
	} 
	

} 
