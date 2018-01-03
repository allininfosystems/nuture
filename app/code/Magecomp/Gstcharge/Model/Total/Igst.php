<?php

namespace Magecomp\Gstcharge\Model\Total;

use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\QuoteValidator;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;

class Igst extends AbstractTotal
{

    protected $helperData;

    /**
     * Collect grand total address amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected $quoteValidator = null;

    public function __construct(QuoteValidator $quoteValidator,
                                GstHelper $helperData)
    {
        $this->quoteValidator = $quoteValidator;
        $this->helperData = $helperData;
    }

    public function collect(
        Quote $quote,
        ShippingAssignmentInterface $shippingAssignment,
        Total $total
    )
    {
        parent::collect($quote, $shippingAssignment, $total);
		
        if (!count($shippingAssignment->getItems())) {
            return $this;
        }
   
        $enabled = $this->helperData->isModuleEnabled();
        if ($enabled) {
            $fee = $this->helperData->getIgstCharge();;
            $total->setIgstCharge($fee);
			$quote->setIgstCharge($fee);
            $quote->getShippingAddress()->setIgstCharge($fee);
            
			if($this->helperData->getGstTaxType())
			{
				 $total->setTotalAmount('igst_charge', $fee);
            	 $total->setBaseTotalAmount('igst_charge', $fee);
				 $total->setGrandTotal($total->getGrandTotal() + $fee);
                 $total->setBaseGrandTotal($total->getBaseGrandTotal() + $fee);
			}
			else{
				 $total->setGrandTotal($total->getGrandTotal() );
		         $total->setBaseGrandTotal($total->getBaseGrandTotal() );
			}
        }
        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
    {

        $enabled = $this->helperData->isModuleEnabled();
        $subtotal = $quote->getSubtotal();
        $fee = $this->helperData->getIgstCharge();
        if ($enabled) {
            return [
                'code' => 'igst_charge',
                'title' => 'igst',
                'value' => $fee
            ];
        } else {
            return array();
        }
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('IGST');
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     */
    protected function clearValues(Total $total)
    {
       // $enabled = $this->helperData->isModuleEnabled();
      
       // $subtotal = $total->getTotalAmount('subtotal');
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);

    }
}
