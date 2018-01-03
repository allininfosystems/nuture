<?php

namespace Nuluv\Freeshipamt\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;

class Freeshipamt extends AbstractTotal {

	public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo) {

		$order = $creditmemo -> getOrder();

		$percent = $creditmemo -> getSubtotal() / $order -> getSubtotal();

		$creditmemo -> setFreeshipamt(0);
		$creditmemo -> setBaseFreeshipamt(0);

		$amount = $creditmemo -> getOrder() -> getFreeshipamt() * $percent;
		$baseAmount = $creditmemo -> getOrder() -> getBaseFreeshipamt() * $percent;

		$creditmemo -> setFreeshipamt($amount);

		$creditmemo -> setBaseFreeshipamt($baseAmount);

		$creditmemo -> setGrandTotal($creditmemo -> getGrandTotal() + $amount);
		$creditmemo -> setBaseGrandTotal($creditmemo -> getBaseGrandTotal() + $baseAmount);

		return $this;
	} 
} 