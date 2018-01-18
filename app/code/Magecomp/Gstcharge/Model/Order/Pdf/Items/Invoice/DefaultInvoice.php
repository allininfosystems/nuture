<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magecomp\Gstcharge\Model\Order\Pdf\Items\Invoice;

use Magento\Sales\Model\Order\Pdf\Items\Invoice\DefaultInvoice as InvoiceDefualt;
/**
 * Sales Order Invoice Pdf default items renderer
 */
class DefaultInvoice extends InvoiceDefualt
{

    public function draw()
    {
        $order = $this->getOrder();
        $item = $this->getItem();
        $pdf = $this->getPdf();
        $page = $this->getPage();
        $lines = [];
		$hsncode = $this->getHsncodeValue($item);
		$taxableAmount=$item->getrow_total() - $item->getdiscount_amount();
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$customproduct = $objectManager->create('Magento\Catalog\Model\Product')->load($item->getId());

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
		
		$namearra = $this->string->split( $item->getName(), 20);
		
		if($customproduct->getExpiryDate()){
			$namearra[] = "Expiry Date-: ".date("d/m/Y",strtotime($customproduct->getExpiryDate()));
		}
		
		$namearra[] = "HSN: ".$hsncode;
		
        $lines[0] = [['text' => $namearra, 'feed' => 30]];
		$lines[0][] = ['text' => $item->getQty() * 1, 'feed' => 150, 'align' => 'right'];
		// draw Price
            $lines[0][] = [
                'text' => round($item->getPrice(),2),
                'feed' => 185,
                'font' => 'bold',
                'align' => 'right',
            ];
        
		// draw Subtotal
		 $lines[0][] = [
                'text' => round($subTotal, 2),
                'feed' => 235,
                'font' => 'bold',
                'align' => 'right',
            ];
		// draw Discount
        $lines[0][] = array(
            'text'  => round($item->getdiscount_amount(),2),
            'feed'  => 290,
            'font'  => 'bold',
            'align' => 'right'
        );	
		// draw Tax	
        $lines[0][] = [
            'text' => $taxableAmount,
            'feed' => 345,
            'font' => 'bold',
            'align' => 'right',
        ];
		
		
		// draw Tax
		//$cgst = $this->string->split( $order->formatPriceTxt($item->getcgst_charge()), 10);
		$cgst = $this->string->split($item->getcgst_charge(), 10);
		$cgst[] = "(".floatval($item->getcgst_percent())."%)";
        $lines[0][] = [
            'text' => $cgst,
            'feed' => 400,
            'font' => 'bold',
            'align' => 'right',
        ];
		// draw Tax
		$sgst = $this->string->split($item->getsgst_charge(), 10);
		$sgst[] = "(".floatval($item->getsgst_percent())."%)";
        $lines[0][] = [
            'text' => $sgst,
            'feed' => 455,
            'font' => 'bold',
            'align' => 'right',
        ];
		// draw Tax
		$igst = $this->string->split($item->getigst_charge(), 10);
		$igst[] = "(".floatval($item->getigst_percent())."%)";
        $lines[0][] = [
            'text' => $igst,
            'feed' => 510,
            'font' => 'bold',
            'align' => 'right',
        ];
		// draw TOTAL AMOUNT
        $lines[0][] = array(
            'text'  => round($itemTotal,2),
            'feed'  => 570,
            'font'  => 'bold',
            'align' => 'right'
        );
        // custom options
        $options = $this->getItemOptions();
        if ($options) {
            foreach ($options as $option) {
                // draw options label
                $lines[][] = [
                    'text' => $this->string->split($this->filterManager->stripTags($option['label']), 40, true, true),
                    'font' => 'italic',
                    'feed' => 35,
                ];

                if ($option['value']) {
                    if (isset($option['print_value'])) {
                        $printValue = $option['print_value'];
                    } else {
                        $printValue = $this->filterManager->stripTags($option['value']);
                    }
                    $values = explode(', ', $printValue);
                    foreach ($values as $value) {
                        $lines[][] = ['text' => $this->string->split($value, 30, true, true), 'feed' => 40];
                    }
                }
            }
        }
		 
		
        $lineBlock = ['lines' => $lines, 'height' => 20];

        $page = $pdf->drawLineBlocks($page, [$lineBlock], ['table_header' => true]);
        $this->setPage($page);
    }
	private function getHsncodeValue($item)
	{
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$prod = $objectManager->get('Magento\Catalog\Model\Product')->load($item->getProductId());
	
		if(!($return_location = $prod->getHsncode()))
			return 'N/A';
		else
			return $return_location;
	}
	
}
