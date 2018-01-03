<?php

namespace Magecomp\Gstcharge\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Magento\Sales\Model\Order\Creditmemo;
use Magecomp\Gstcharge\Helper\Data as GstHelper;

class Shippingigst extends AbstractTotal
{
    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return $this
     */
	 protected $helperData;
	 public function __construct(
     GstHelper $helperData)
    {
        $this->helperData = $helperData;
    }  
    public function collect(Creditmemo $creditmemo)
    {
        $amount = $creditmemo->getOrder()->getShippingIgstCharge();
        $creditmemo->setShippingIgstCharge($amount);

		if($this->helperData->getShippingGstTaxType())
		{
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $creditmemo->getShippingIgstCharge());
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $creditmemo->getShippingIgstCharge());
		}else{
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal());
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() );
		}
        return $this;
    }
}
