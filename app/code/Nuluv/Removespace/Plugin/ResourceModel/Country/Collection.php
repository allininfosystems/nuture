<?php

namespace Nuluv\Removespace\Plugin\ResourceModel\Country;

/**
 * Plugin for processing incoming arguments of the method that leading to displaying additional empty dropdown.
 */
class Collection
{
    /**
     * Arguments processing.
     *
     * @param \Magento\Directory\Model\ResourceModel\Country\Collection $subject
     * @param bool $emptyLabel
     *
     * @return bool|array
     */
    public function beforeToOptionArray(
        \Magento\Directory\Model\ResourceModel\Country\Collection $subject,
        $emptyLabel = null
    ) {
        return is_null($emptyLabel) ? [''] : null;
    }
}
 ?>