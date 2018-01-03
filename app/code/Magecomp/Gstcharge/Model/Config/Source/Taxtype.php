<?php
namespace Magecomp\Gstcharge\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Taxtype implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Excluding Tax')],
			['value' => 0, 'label' => __('Including Tax')],
        ];
    }
}

?>