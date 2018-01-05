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

class Customerbehavior
{
    /**
     * @return array
     */
    public static function toArray()
    {
        $result = [];

        $types = [
            'poll',
            'tag',
        ];

        foreach ($types as $type) {
            $object = Mage::getSingleton('rewards/earning_behavior_'.$type.'_behavior');
            $result[$object->getActionCode()] = $object->getTitle();
        }

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
