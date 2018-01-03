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
class SpendingLimit implements OptionSourceInterface
{

    public function toOptionArray()
    {
        return [
            ['label'=> __('None'),'value'=>'none'],
            ['value' => 'by_price','label' => __('A fixed amount of Total Order Value')],
            ['value' => 'by_percent','label' => __('A percentage of Total Order Value')],
        ];
    }
}
