<?php

namespace WIO\Forum\Model\Source;

class Layout implements \Magento\Framework\Option\ArrayInterface{
    
    public function toOptionArray(){
        return [
            ['value' => '1column', 'label' => __('1 column')], 
            ['value' => '2columns-left', 'label' => __('2 columns left')], 
            ['value' => '2columns-right', 'label' => __('2 columns right')]
        ];
    }
    
    public function toArray(){
        return [
            '1column' => __('1 column'), 
            '2columns-left'  => __('2 columns left'),
            '2columns-right' => __('2 columns right')
        ];
    }
}
