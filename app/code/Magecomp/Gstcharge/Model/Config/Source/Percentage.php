<?php
namespace Magecomp\Gstcharge\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
class Percentage implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => -1, 'label' => __('None')],
			['value' => 0, 'label' => __('0%')],
			['value' => 0.25, 'label' => __('0.25%')],
			['value' => 3, 'label' => __('3%')],
            ['value' => 5, 'label' => __('5%')],
			['value' => 12, 'label' => __('12%')],
			['value' => 18, 'label' => __('18%')],
			['value' => 24, 'label' => __('24%')],			  
			['value' => 28, 'label' => __('28%')],			  
			
        ];
    }
}
