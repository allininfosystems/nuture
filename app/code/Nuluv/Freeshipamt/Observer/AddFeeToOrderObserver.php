<?php

namespace Nuluv\Freeshipamt\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddFeeToOrderObserver implements ObserverInterface
{

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $quote = $observer->getQuote();
		$order = $observer->getOrder();

		
        $CustomFeeFreeshipamt = $quote->getFreeshipamt();
        $CustomFeeBaseFreeshipamt = $quote->getBaseFreeshipamt();

        if ($CustomFeeFreeshipamt&&$CustomFeeBaseFreeshipamt) {

			$order->setData('freeshipamt', $CustomFeeFreeshipamt);
			$order->setData('base_freeshipamt', $CustomFeeBaseFreeshipamt);

        }


        return $this;

    }
}
