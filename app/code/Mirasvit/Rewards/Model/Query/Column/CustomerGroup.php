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


namespace Mirasvit\Rewards\Model\Query\Column;

use Magento\Framework\Api\FilterBuilder;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as GroupCollection;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;
use Mirasvit\Rewards\Model\Query\Column;

class CustomerGroup extends Column
{
    public function __construct(
        GroupCollection $groupCollection,
        FilterBuilder $filterBuilder,
        MapRepositoryInterface $mapRepository,
        $name,
        $data = []
    ) {
        parent::__construct($filterBuilder, $mapRepository, $name, $data);

        $this->groupCollection = $groupCollection;
    }

    /**
     * @return array
     */
    public function getJsConfig()
    {
        $options = [[
            'label'            => __('All Customer Groups'),
            'type'             => 'all',
            'customerGroupIds' => '0',
        ]];

        $groups = $this->groupCollection->create();

        /** @var \Magento\Customer\Model\Group $group */
        foreach ($groups as $group) {
            $options[] = [
                'label'            => $group->getCustomerGroupCode(),
                'type'             => 'customer_group',
                'customerGroupIds' => $group->getId() ?: -1,
            ];
        }

        return [
            'component'      => 'Mirasvit_Rewards/js/report/toolbar/filter/customer/group',
            'value'          => null,
            'current'        => __('All Customer Groups'),
            'customerGroups' => $options,
            'column'         => $this->getName(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function prepareValue($value)
    {
        return $this->groupCollection->create()->getItemById($value)->getCustomerGroupCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getIsHidden()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDisabled()
    {
        return true;
    }
}
