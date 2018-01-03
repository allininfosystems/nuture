<?php
namespace Magecomp\Gstcharge\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magecomp\Gstcharge\Model\CgstFeeConfigProvider;
use Magecomp\Gstcharge\Helper\Data as GstHelper;
use Magento\Tax\Model\Config;
use	Magento\Catalog\Model\ProductFactory;
use	Magento\Catalog\Model\CategoryFactory;
use	Magento\Quote\Model\QuoteFactory;
use Magento\Framework\Event\Observer;

class Gstorder implements ObserverInterface
{
    /**
     * @var \Magecomp\Surcharge\Model\Quote\Address\Total\SurchargeFactory
     */
    protected $_totalSurchargeFactory;

    /**
     * @var \Magecomp\Surcharge\Helper\Data
     */
    protected $_helperData;

    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_modelConfig;
	protected $_productloader;
	protected $_categoryFactory;
	protected $quoteFactory;
    public function __construct(CgstFeeConfigProvider $totalSurchargeFactory, 
        GstHelper $helperData, 
        Config $modelConfig,
		ProductFactory $_productloader,
		CategoryFactory $categoryFactory,
		QuoteFactory $quoteFactory)
    {
        $this->_totalSurchargeFactory = $totalSurchargeFactory;
        $this->_helperData = $helperData;
        $this->_modelConfig = $modelConfig;
		$this->_categoryFactory = $categoryFactory;
        $this->_productloader = $_productloader;
		$this->quoteFactory = $quoteFactory;

    }

    public function execute(Observer $observer)
    {
       try
		{
			$order = $observer->getOrder();
			if(!($this->_helperData->isModuleEnabled()))
			{
				return 0;
			}
			$quoteId=$order->getQuoteId();
			$quote = $this->quoteFactory->create()->load($quoteId);
			$shippingAddress = $order->getShippingAddress();
			
			$excludingTax=$quote->getExclPrice();
			if($shippingAddress)
			{
				$CountryId=$shippingAddress->getCountryId();
				$CountryId=$shippingAddress->getCountryId();
				$CustomerRegionId=$shippingAddress->getRegionId();
				$SystemRegionId=$this->_helperData->getGstStateConfig();
				if($CountryId!='IN')
				{
					  return 0;
				}
				$TotalGstPrice=0;
				$totalCgstPrice = $totalSgstPrice = $totalIgstPrice = 0;
				foreach ($order->getAllVisibleItems() as $item) 
				{
					  // $excludingTax=$item->getExclPrice();
					   $gstPercent=0;
					   $product= $this->_productloader->create()->load($item->getProductId());
					   $itemPriceAfterDiscount= ($item->getPrice() * $item->getDiscountPercent())/100 ;
					   $prdPrice=$item->getPrice()-$itemPriceAfterDiscount;
					   
					   $gstPercent=$product->getGstSource();
					   $gstPercentMinPrice=$product->getGstSourceMinprice();
					   $gstPercentAfterMinprice=$product->getGstSourceAfterMinprice();
					   
					   if($gstPercent<=0)
					   {
						   $cats = $product->getCategoryIds();
						   foreach ($cats as $category_id) 
						   {
								$_cat =$this->_categoryFactory->create()->load($category_id) ;
								
								$gstPercent=$_cat->getGstCatSource();
								$gstPercentMinPrice=$_cat->getGstCatSourceMinprice();
					   			$gstPercentAfterMinprice=$_cat->getGstCatSourceAfterMinprice();
								
								if($gstPercent!='')
								{
									if($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice )   
									{
										$gstPercent=$gstPercentAfterMinprice;
									}
									break;
								}
						   }   
					   }
					   else
					   {
							if($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice )   
							{
								$gstPercent=$gstPercentAfterMinprice;
							}
					   }
					  if($gstPercent<=0)
					  {
						  $gstPercent				=	$this->_helperData->getGstTaxperConfig();
						  $gstPercentMinPrice		=	$this->_helperData->getGstTaxMinPriceConfig();
					   	  $gstPercentAfterMinprice	=	$this->_helperData->getGstTaxPerMinPriceConfig();
						  
						  if($gstPercentMinPrice > 0 && $gstPercentMinPrice > $prdPrice )   
						  {
								$gstPercent=$gstPercentAfterMinprice;
						  }
					  }
					   $productPrice = $item->getProduct()->getPrice();
					   $qty          = $item->getQty();
					   $rowTotal     = $item->getRowTotal();
					   $DiscountAmount=$item->getDiscountAmount();
					

					   if($excludingTax)
					   	{
							$GstPrice= ((($rowTotal-$DiscountAmount)*$gstPercent)/100); 
						}
					   	else
					   	{
																					
						    $totalPercent = 100 + $gstPercent;
							$perPrice     = ($rowTotal-$DiscountAmount) / $totalPercent;
							$GstPrice     = $perPrice * $gstPercent;
						}
						
						//$GstPrice= ((($rowTotal-$DiscountAmount)*$gstPercent)/100);
					   $TotalGstPrice+=$GstPrice;

					   if($CountryId=='IN' && $CustomerRegionId==$SystemRegionId)
					   {
						   	$item->setCgstCharge($GstPrice/2);
						   	$item->setCgstPercent($gstPercent/2);
						   	$item->setSgstCharge($GstPrice/2);
						   	$item->setSgstPercent($gstPercent/2);
						   	$totalCgstPrice += $GstPrice/2;
							$totalSgstPrice += $GstPrice/2;
					   }
					   else if ($CountryId=='IN' && $CustomerRegionId!=$SystemRegionId)
					   {
						     $item->setIgstCharge($GstPrice);
							 $item->setIgstPercent($gstPercent);
							 $totalIgstPrice += $GstPrice; 
					
					   }
					   
					   $item->setExclPrice($excludingTax);
				}
				$order->setSgstCharge($totalSgstPrice);
				$order->setCgstCharge($totalCgstPrice);
				$order->setIgstCharge($totalIgstPrice);
				
				$quoteShippAddress = $quote->getShippingAddress();
				$order->setPercentShippingCgstCharge($quoteShippAddress->getPercentShippingCgstCharge());
				$order->setShippingCgstCharge($quoteShippAddress->getShippingCgstCharge());
				$order->setPercentShippingSgstCharge($quoteShippAddress->getPercentShippingSgstCharge());
				$order->setShippingSgstCharge($quoteShippAddress->getShippingSgstCharge());
				$order->setPercentShippingIgstCharge($quoteShippAddress->getPercentShippingIgstCharge());
				$order->setShippingIgstCharge($quoteShippAddress->getShippingIgstCharge());
				
				$order->save();
				
			}
		}
		catch(Exception $e)
		{
					
			
		}
	}
	
	
}