<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Ui\Component\Listing\Column;
use Magento\Framework\Data\OptionSourceInterface;
/**
 * Class Options
 */
class Status implements OptionSourceInterface
{

    public function toOptionArray()
    {
        return [
            ['label' => __('Active'),'value' => 1],
            ['label' => __('Inactive'),'value' => 2],
        ];
    }
}
