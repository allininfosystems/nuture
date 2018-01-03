<?php
namespace Webinse\Barcode\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class ImageFormats implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [
            ['label' => 'jpeg', 'value' => 'jpeg'],
            ['label' => 'png', 'value' => 'png'],
            //['label' => 'svg', 'value' => 'svg'],
        ];

        return $options;
    }
}