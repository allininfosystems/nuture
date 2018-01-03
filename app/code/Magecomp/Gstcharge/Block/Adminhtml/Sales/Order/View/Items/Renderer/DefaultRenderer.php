<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magecomp\Gstcharge\Block\Adminhtml\Sales\Order\View\Items\Renderer;

use Magento\Sales\Model\Order\Item;
use Magento\Sales\Block\Adminhtml\Order\View\Items\Renderer\DefaultRenderer as MagentoDefualtRenderer;
use Magento\Framework\DataObject;
/**
 * Adminhtml sales order item renderer
 */
class DefaultRenderer extends MagentoDefualtRenderer
{
   
    public function getColumnHtml(DataObject $item, $column, $field = null)
    {
		
        $html = '';
						$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
						$product = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getProductId());
						$hsnCode=$product->getHsncode();
						if(!$product->getHsncode())
						{
							$hsnCode =  'N/A';
						}
                    	$taxableAmount=$item->getrow_total() - $item->getdiscount_amount();
						if($item->getExclPrice())
						{
							$subTotal = $item->getrow_total();
							$itemTotal = $subTotal + $item->getcgst_charge() + $item->getsgst_charge() + $item->getigst_charge();
						}
						else
						{
							$subTotal = $item->getrow_total() - $item->getcgst_charge() - $item->getsgst_charge() - $item->getigst_charge();
							$taxableAmount=$subTotal - $item->getdiscount_amount();
							$itemTotal = $taxableAmount + $item->getcgst_charge() + $item->getsgst_charge() + $item->getigst_charge();
						}
								

        switch ($column) {
            case 'product':
                if ($this->canDisplayContainer()) {
                    $html .= '<div id="' . $this->getHtmlId() . '">';
                }
                $html .= $this->getColumnHtml($item, 'name')."HSN CODE : ".$hsnCode;
                if ($this->canDisplayContainer()) {
                    $html .= '</div>';
                }
                break;
           /* case 'status':
                $html = $item->getStatus();
                break;*/
           /* case 'price-original':
                $html = $this->displayPriceAttribute('original_price');
                break;*/
			case 'price':
                $html = $this->displayPriceAttribute('price');
                break;	
			case 'subtotal':
                $html = $objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency($subTotal);
                break;		
			 case 'discont':
                $html = $this->displayPriceAttribute('discount_amount');
                break;	
			case 'tax-amount':
                $html = $objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency($taxableAmount);
                break;	
			case 'cgst':
                $html = $html = $this->displayPriceAttribute('cgst_charge')."<div>".$item->getcgst_percent()."%</div>";
                break;	
			case 'sgst':
                $html = $this->displayPriceAttribute('sgst_charge')."<div>".$item->getsgst_percent()."%</div>";
                break;	
			case 'igst':
                $html = $this->displayPriceAttribute('igst_charge')."<div>".$item->getigst_percent()."%</div>";
                break;	
			case 'total':
                $html = $objectManager->get('Magento\Framework\Pricing\Helper\Data')->currency($itemTotal);
                break;				
            
           /* case 'tax-percent':
                $html = $this->displayTaxPercent($item);
                break;*/
           
            default:
                $html = parent::getColumnHtml($item, $column, $field);
        }
        return $html;
    }

    
}
