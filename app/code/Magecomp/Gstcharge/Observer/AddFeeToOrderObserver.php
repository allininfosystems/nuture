<?php
namespace Magecomp\Gstcharge\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AddFeeToOrderObserver implements ObserverInterface
{
    /**
     * Set payment fee to order
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $quote = $observer->getQuote();
        $CgstfeeFee = $quote->getShippingAddress()->getCgstCharge();
        $SgstfeeFee = $quote->getShippingAddress()->getSgstCharge();
		$IgstfeeFee = $quote->getShippingAddress()->getIgstCharge();		
        /*if (!$CgstfeeFee) {
            return $this;
        }
		if(!$SgstfeeFee){
			return $this;
		}
		if (!$IgstfeeFee) {
            return $this;
        }*/
        //Set fee data to order
        $order = $observer->getOrder();
        $order->setData('cgst_charge', $CgstfeeFee);
		$order->setData('sgst_charge', $SgstfeeFee);
		$order->setData('igst_charge', $IgstfeeFee);
        
		return $this;
    }
}
