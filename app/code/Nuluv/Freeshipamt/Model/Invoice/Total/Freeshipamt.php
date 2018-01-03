<?php

namespace Nuluv\Freeshipamt\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;

class Freeshipamt extends AbstractTotal {

	public function collect(\Magento\Sales\Model\Order\Invoice $invoice) {

		$order = $invoice -> getOrder();

		$percent = $invoice -> getSubtotal() / $order -> getSubtotal();

		$invoice -> setFreeshipamt(0);
		$invoice -> setBaseFreeshipamt(0);

		$amount = $invoice -> getOrder() -> getFreeshipamt() * $percent;

		$baseAmount = $invoice -> getOrder() -> getBaseFreeshipamt() * $percent;

		$invoice -> setFreeshipamt($amount);

		$invoice -> setBaseFreeshipamt($baseAmount);

		$invoice -> setGrandTotal($invoice -> getGrandTotal() + $amount);
		$invoice -> setBaseGrandTotal($invoice -> getBaseGrandTotal() + $invoice -> getBaseFreeshipamt() * $baseAmount);

		return $this;
	} 
} 