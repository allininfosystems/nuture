<?php

namespace Nuluv\Freeshipamt\Model\Quote\Total;

use Magento\Store\Model\ScopeInterface;
use Magento\Checkout\Model\Session as CheckoutSession;

class Freeshipamt extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal {

	protected $helperData;
	
	protected $_checkoutSession;

	protected $quoteValidator = null;

	public function __construct(\Magento\Quote\Model\QuoteValidator $quoteValidator,
		\Nuluv\Freeshipamt\Helper\Data $helperData, CheckoutSession $checkoutSession) {
		$this -> quoteValidator = $quoteValidator;
		$this -> helperData = $helperData;
		$this->_checkoutSession = $checkoutSession;
	} 

	public function collect(\Magento\Quote\Model\Quote $quote,
		\Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
		\Magento\Quote\Model\Quote\Address\Total $total
		) {
		parent :: collect($quote, $shippingAssignment, $total);
		if (!count($shippingAssignment -> getItems())) {
			return $this;
		} 
		$enabled = $this -> helperData -> isModuleEnabledFreeshipamt();
		$minimumOrderAmount = $this -> helperData -> getMinimumOrderAmountFreeshipamt();
		$subtotal = $total -> getTotalAmount('subtotal');
		$shippingAddress = $this->_checkoutSession->getQuote()->getShippingAddress();
		if($shippingAddress){
			$getShippingRate = $shippingAddress->getShippingAmount();
			if($getShippingRate > 0){
				$freeshipamt = 0 - intval($getShippingRate);	
			}else{
				$freeshipamt = 0;
			}
			
		}else{
			$freeshipamt = 0;
		}
		
		if ($enabled && $minimumOrderAmount <= $subtotal) {
			
			$total -> setTotalAmount('freeshipamt', $freeshipamt);
			$total -> setBaseTotalAmount('freeshipamt', $freeshipamt);
			$total -> setFreeshipamt($freeshipamt);
			$total -> setBaseFreeshipamt($freeshipamt);
			$quote -> setFreeshipamt($freeshipamt);
			$quote -> setBaseFreeshipamt($freeshipamt);
			$total -> setGrandTotal($total -> getGrandTotal() + $freeshipamt);
			$total -> setBaseGrandTotal($total -> getBaseGrandTotal() + $freeshipamt);
		} 
		return $this;
	} 

	public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total) {
		$enabled = $this -> helperData -> isModuleEnabledFreeshipamt();
		$minimumOrderAmount = $this -> helperData -> getMinimumOrderAmountFreeshipamt();
		$subtotal = $quote -> getSubtotal();
		$freeshipamt = $quote -> getFreeshipamt();

		$result = [];
		if ($enabled && ($minimumOrderAmount <= $subtotal) && $freeshipamt) {
			$result = [
			'code' => 'freeshipamt',
			'title' => $this -> helperData -> getFeeLabelFreeshipamt(),
			'value' => $freeshipamt
			];
		} 
		return $result;
	} 

	public function getLabel() {
		return __('Free ship amt');
	} 

	protected function clearValues(\Magento\Quote\Model\Quote\Address\Total $total) {
		$total -> setTotalAmount('subtotal', 0);
		$total -> setBaseTotalAmount('subtotal', 0);
		$total -> setTotalAmount('tax', 0);
		$total -> setBaseTotalAmount('tax', 0);
		$total -> setTotalAmount('discount_tax_compensation', 0);
		$total -> setBaseTotalAmount('discount_tax_compensation', 0);
		$total -> setTotalAmount('shipping_discount_tax_compensation', 0);
		$total -> setBaseTotalAmount('shipping_discount_tax_compensation', 0);
		$total -> setSubtotalInclTax(0);
		$total -> setBaseSubtotalInclTax(0);
	} 
}  