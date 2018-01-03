<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction;
use Magento\Framework\Data\OptionSourceInterface;
/**
 * Class Options
 */
class Status implements OptionSourceInterface
{
    const STATUS_PENDING = 1;
    const STATUS_ON_HOLD = 2;
    const STATUS_COMPLETED = 3;
    const STATUS_CANCELED = 4;
    const STATUS_EXPIRED = 5;
    const ACTION_TYPE_BOTH = 0;
    const ACTION_TYPE_EARN = 1;
    const ACTION_TYPE_SPEND = 2;

    public function toOptionHash() {
        return array(
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_ON_HOLD => __('On Hold'),
            self::STATUS_COMPLETED => __('Complete'),
            self::STATUS_CANCELED => __('Canceled'),
            self::STATUS_EXPIRED => __('Expired'),
        );
    }

    public function toOptionArray() {
        $options = array();
        foreach ($this->toOptionHash() as $value => $label) {
            $options[] = array(
                'value' => $value,
                'label' => $label,
            );
        }
        return $options;
    }
}
