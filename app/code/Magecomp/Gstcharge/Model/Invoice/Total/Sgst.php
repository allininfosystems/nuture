<?php

namespace Magecomp\Gstcharge\Model\Invoice\Total;

use Magento\Sales\Model\Order\Invoice\Total\AbstractTotal;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Sales\Model\Order\Invoice;


class Sgst extends AbstractTotal
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
        $invoice->getIgstCharge(0);
	    $amount = $invoice->getOrder()->getSgstCharge();
        $invoice->setSgstCharge($amount);

		if($this->helperData->getGstTaxType())
		{
	        $invoice->setGrandTotal($invoice->getGrandTotal() + $invoice->getSgstCharge());
    	    $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $invoice->getSgstCharge());
		}
		else{
			$invoice->setGrandTotal($invoice->getGrandTotal());
	        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() );	
		}
        return $this;
    }
}
