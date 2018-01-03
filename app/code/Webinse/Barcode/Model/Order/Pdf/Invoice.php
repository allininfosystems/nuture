<?php
namespace Webinse\Barcode\Model\Order\Pdf;

class Invoice extends \Magento\Sales\Model\Order\Pdf\Invoice
{
    public function getPdf($invoices = [])
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                $this->_localeResolver->emulate($invoice->getStoreId());
                $this->_storeManager->setCurrentStore($invoice->getStoreId());
            }
            $page = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                $this->_scopeConfig->isSetFlag(
                    self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $order->getStoreId()
                )
            );
            /* Add document text and number */
            $this->insertDocumentNumber($page, __('Invoice # ') . $invoice->getIncrementId());
            /* Add table */
            $this->_drawHeader($page);
            /* Add body */
            foreach ($invoice->getAllItems() as $item) {
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }
            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                $this->_localeResolver->revert();
            }

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $modelGenerator = $objectManager->create('Webinse\Barcode\Model\Generator');
            $modelConfig = $objectManager->create('Webinse\Barcode\Model\Config');

            if ($modelConfig->isEnableBarcodeInvoice()) {
                $items = $invoice->getAllItems();
                $printedHeader = false;
                $leftOffset = 35;
                $topOffset = 0;

                foreach ($items as $item) {
                    $model = $modelGenerator->load($item->getSku(), "sku");
                    $barcodeNumber = $model->getBarcode();

                    if (!$printedHeader && $barcodeNumber) {
                        $this->_drawBarcodeHeader($page, $items);
                        $printedHeader = true;
                    }

                    if ($barcodeNumber) {
                        $filename = 'var/tmp/barcode' . $barcodeNumber . '.' . $model->getImageFormat();
                        file_put_contents($filename, base64_decode($model->getEncodedImage()));
                        $image = \Zend_Pdf_Image::imageWithPath($filename);
                        $page->setFont(\Zend_Pdf_Font::fontWithName(\Zend_Pdf_Font::FONT_HELVETICA), 10);

                        if (595 - $leftOffset - $image->getPixelWidth() > 35) {
                            $this->_drawBarcode($page, $item, $leftOffset, $image);
                            $leftOffset += $image->getPixelWidth() + 20;
                            if ($topOffset < $image->getPixelHeight()) {
                                $topOffset = $image->getPixelHeight();
                            }
                        } else {
                            $leftOffset = 35;
                            $this->y -= $topOffset + 25;
                            $this->_drawBarcode($page, $item, $leftOffset, $image);
                            $leftOffset += $image->getPixelWidth() + 20;
                            $topOffset = $image->getPixelHeight();
                        }
                    }

                    if ($barcodeNumber && end($items) === $item) {
                        $this->y -= $image->getPixelHeight() + 15;
                    }
                }

                $page->drawLine(25, $this->y, 570, $this->y);
            }
        }
        $this->_afterGetPdf();
        return $pdf;
    }

    protected function _drawBarcodeHeader(\Zend_Pdf_Page $page, $items)
    {
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y - 15);
        $this->y -= 10;
        $this->_setFontBold($page, 12);
        $page->setFillColor(new \Zend_Pdf_Color_RGB(0, 0, 0));
        $page->drawText(count($items) > 1 ? 'Barcodes: ' : 'Barcode: ', 35, $this->y - 1);
        $this->y -= 25;
    }

    protected function _drawBarcode(\Zend_Pdf_Page $page, $item, $leftOffset, $image)
    {
        $page->drawText('SKU: ' . $item->getSku(), $leftOffset, $this->y);
        $page->drawImage(
            $image,
            $leftOffset,
            $this->y - $image->getPixelHeight() - 2,
            $leftOffset + $image->getPixelWidth(),
            $this->y - 2
        );
    }
}