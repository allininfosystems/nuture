<?php
namespace Magecomp\Gstcharge\Model\Quote\Total;

use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Quote\Model\QuoteValidator;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;

class Shippingigst extends AbstractTotal
{

    protected $helperData;
	protected $_priceCurrency;

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
								PriceCurrencyInterface $priceCurrency,
                                GstHelper $helperData)
    {
        $this->quoteValidator = $quoteValidator;
		$this->_priceCurrency = $priceCurrency;
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
 		 $isShippingEnabled = $this->helperData->isGstApplyOnShpping();
		 if ($enabled && $isShippingEnabled) 
		{ 
				$address = $quote->getShippingAddress();
				$countryId = $address->getCountryId();
				$customerRegionId = $address->getRegionId();
				$systemRegionId = $this->helperData->getGstStateConfig();
				
				$maxGstPercent = $gstPercent = 0;
				foreach ($quote->getAllVisibleItems() as $item) 
				{
					
					if($countryId == 'IN' && $customerRegionId!=$systemRegionId)
					{
						$gstPercent = $item->getIgstPercent();
						
					}
					if ($gstPercent > $maxGstPercent)
						$maxGstPercent = $gstPercent;
				}
				
				 if($this->helperData->getShippingGstTaxType()):
					$shippingGst = $address->getShippingAmount() * ($maxGstPercent/100);
					
				else:
					$shippingGstTotal = 100 + $maxGstPercent;
					$shippingGstPeracent = $address->getShippingAmount() / $shippingGstTotal;
					$shippingGst = $shippingGstPeracent * $maxGstPercent;
				endif;
				
				$address->setPercentShippingIgstCharge($maxGstPercent);
				$address->setShippingIgstCharge($shippingGst);
			if($this->helperData->getShippingGstTaxType()){	
				$total->setTotalAmount('shipping_igst_charge', $shippingGst);
	            $total->setBaseTotalAmount('shipping_igst_charge', $shippingGst);
				$total->setGrandTotal($total->getGrandTotal() + $address->getShippingIgstCharge());
        		$total->setBaseGrandTotal($total->getBaseGrandTotal() + $address->getShippingIgstCharge());
			}else{
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
	    $address = $quote->getShippingAddress();
     
	    $fee = $address->getShippingIgstCharge();
        $result = array();
        if ($enabled) {
            $result = [
                'code' => 'shipping_igst_charge',
                'title' => 'Shipping Igst',
                'value' => $fee
            ];
        }
        return $result;
    }

    /**
     * Get Subtotal label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getLabel()
    {
        return __('shipping_igst_charge Fee');
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     */
    protected function clearValues(Total $total)
    {
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
