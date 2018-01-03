<?php

namespace Magecomp\Gstcharge\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Sales\Model\Order\Invoice;

class Shippingsgst extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @return $this
     */
	 protected $helperData;
	 public function __construct(
     GstHelper $helperData)
     {
        $this->helperData = $helperData;
     }  
    public function collect(Invoice $invoice)
    {
        $invoice->getShippingIgstCharge(0);
	    $amount = $invoice->getOrder()->getShippingSgstCharge();
        $invoice->setShippingSgstCharge($amount);

		if($this->helperData->getShippingGstTaxType())
		{
	        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getShippingSgstCharge());
    	    $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getShippingSgstCharge());
		}
		else{
			$invoice->setGrandTotal($invoice->getGrandTotal());
	        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() );	
		}
        return $this;
    }
}
