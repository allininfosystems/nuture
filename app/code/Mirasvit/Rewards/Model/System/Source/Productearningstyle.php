<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rewards\Model\System\Source;

use Mirasvit\Rewards\Model\Config as Config;

class Productearningstyle
{
    /**
     * @return array
     */
    public static function toArray()
    {
        $result = [
            Config::EARNING_STYLE_GIVE => __('Give X points to customer'),
            Config::EARNING_STYLE_AMOUNT_PRICE => __('For every Y, give X points'),
        ];

        return $result;
    }

    /**
     * @return array
     */
    public static function toOptionArray()
    {
        $options = self::toArray();
        $result = [];

        foreach ($options as $key => $value) {
            $result[] = [
                'value' => $key,
                'label' => $value,
            ];
        }

        return $result;
    }
}
