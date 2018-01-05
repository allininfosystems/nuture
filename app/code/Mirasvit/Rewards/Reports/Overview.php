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


namespace Mirasvit\Rewards\Reports;

use Mirasvit\Report\Model\AbstractReport;

class Overview extends AbstractReport
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return __('Reward Points');
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentifier()
    {
        return 'rewards_overview';
    }

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->setBaseTable('mst_rewards_points_aggregated_hour');
        $this->addFastFilters([
            'mst_rewards_points_aggregated_hour|period_filter',
            'mst_rewards_points_aggregated_hour|customer_group_id',
            'mst_rewards_points_aggregated_hour|store_id',
        ]);
        $this->setDefaultColumns([
            'mst_rewards_points_aggregated_hour|total_points_earned',
            'mst_rewards_points_aggregated_hour|total_points_spent',
            'mst_rewards_points_aggregated_hour|total_points_spent_in_money',
            'mst_rewards_points_aggregated_hour|average_points_earned',
            'mst_rewards_points_aggregated_hour|average_points_spent',
            'mst_rewards_points_aggregated_hour|total_expired_points',
        ]);

        $this->addAvailableColumns(
            $this->context->getMapRepository()
                ->getTable('mst_rewards_points_aggregated_hour')->getColumns()
        );

        $this->setDefaultDimension('mst_rewards_points_aggregated_hour|day');

        $this->addAvailableDimensions([
            'mst_rewards_points_aggregated_hour|hour_of_day',
            'mst_rewards_points_aggregated_hour|day',
            'mst_rewards_points_aggregated_hour|week',
            'mst_rewards_points_aggregated_hour|month',
            'mst_rewards_points_aggregated_hour|year',
        ]);

        $this->setGridConfig([
            'paging' => true,
        ]);
        $this->setChartConfig([
            'chartType' => 'column',
            'vAxis'     => 'mst_rewards_points_aggregated_hour|total_points_earned',
        ]);
    }
}