<?php
namespace Webinse\Barcode\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class BarcodeValue implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['label' => 'Random value', 'value' => 'random'],
            ['label' => 'Value by pattern', 'value' => 'pattern'],
            ['label' => 'SKU', 'value' => 'sku'],
            ['label' => 'Name', 'value' => 'name'],
            ['label' => 'Price', 'value' => 'price'],
        ];

        return $options;
    }
}