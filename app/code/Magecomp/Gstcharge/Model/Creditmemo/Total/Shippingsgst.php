<?php

namespace Magecomp\Gstcharge\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Magento\Sales\Model\Order\Creditmemo;
use Magecomp\Gstcharge\Helper\Data as GstHelper;

class Shippingsgst extends AbstractTotal
{
     protected $helperData;
	 public function __construct(
     GstHelper $helperData)
    {
        $this->helperData = $helperData;
    }  
    public function collect(Creditmemo $creditmemo)
    {
        $amount = $creditmemo->getOrder()->getShippingSgstCharge();
        $creditmemo->setShippingSgstCharge($amount);
		if($this->helperData->getShippingGstTaxType())
		{
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getShippingSgstCharge());
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getShippingSgstCharge());
		}
		else
		{
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal());
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal());
		}
        return $this;
    }
}
