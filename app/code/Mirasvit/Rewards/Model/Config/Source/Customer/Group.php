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


namespace Mirasvit\Rewards\Model\Config\Source\Customer;

use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as GroupCollection;

class Group implements \Magento\Framework\Option\ArrayInterface
{
    public function __construct(
        GroupCollection $groupCollection
    ) {
        $this->groupCollection = $groupCollection;
    }

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        $data = [];
        $groups = $this->groupCollection->create();
        foreach ($groups as $group) {
            $data[$group->getId()] = $group->getCustomerGroupCode();
        }

        return $data;
    }
}