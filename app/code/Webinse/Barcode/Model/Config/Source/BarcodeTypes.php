<?php
namespace Webinse\Barcode\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class BarcodeTypes implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['label' => 'C39', 'value' => 'C39'],
            ['label' => 'C39+', 'value' => 'C39+'],
            ['label' => 'C39E', 'value' => 'C39E'],
            ['label' => 'C39E+', 'value' => 'C39E+'],
            ['label' => 'C93', 'value' => 'C93'],
            ['label' => 'S25', 'value' => 'S25'],
            ['label' => 'S25+', 'value' => 'S25+'],
            ['label' => 'I25', 'value' => 'I25'],
            ['label' => 'I25+', 'value' => 'I25+'],
            ['label' => 'C128', 'value' => 'C128'],
            ['label' => 'C128A', 'value' => 'C128A'],
            ['label' => 'C128B', 'value' => 'C128B'],
            ['label' => 'C128C', 'value' => 'C128C'],
            ['label' => 'EAN2', 'value' => 'EAN2'],
            ['label' => 'EAN5', 'value' => 'EAN5'],
            ['label' => 'EAN8', 'value' => 'EAN8'],
            ['label' => 'EAN13', 'value' => 'EAN13'],
            ['label' => 'UPCA', 'value' => 'UPCA'],
            ['label' => 'UPCE', 'value' => 'UPCE'],
            ['label' => 'MSI', 'value' => 'MSI'],
            ['label' => 'MSI+', 'value' => 'MSI+'],
            ['label' => 'POSTNET', 'value' => 'POSTNET'],
            ['label' => 'PLANET', 'value' => 'PLANET'],
            ['label' => 'RMS4CC', 'value' => 'RMS4CC'],
            ['label' => 'KIX', 'value' => 'KIX'],
            //['label' => 'IMB', 'value' => 'IMB'],
            ['label' => 'CODABAR', 'value' => 'CODABAR'],
            ['label' => 'CODE11', 'value' => 'CODE11'],
            ['label' => 'PHARMA', 'value' => 'PHARMA'],
            ['label' => 'PHARMA2T', 'value' => 'PHARMA2T'],
        ];

        return $options;
    }
}