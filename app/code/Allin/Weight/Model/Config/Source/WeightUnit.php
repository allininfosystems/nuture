<?php

namespace Allin\Weight\Model\Config\Source;

class WeightUnit extends \Magento\Directory\Model\Config\Source\WeightUnit
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [['value' => 'lbs', 'label' => __('lbs')], ['value' => 'kgs', 'label' => __('kgs')], ['value' => 'gms', 'label' => __('grams')]];
    }
}