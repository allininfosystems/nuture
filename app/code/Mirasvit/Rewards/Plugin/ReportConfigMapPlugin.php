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


namespace Mirasvit\Rewards\Plugin;

use Magento\Framework\App\ResourceConnection;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;

class ReportConfigMapPlugin
{
    /**
     * @var MapRepositoryInterface
     */
    private $mapRepository;

    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(
        MapRepositoryInterface $mapRepository,
        ResourceConnection $resource
    ) {
        $this->mapRepository = $mapRepository;
        $this->resource      = $resource;
    }

    /**
     * @return void
     */
    public function afterLoad()
    {
        $sql = $this->getRuleCodeSql('0000-00-00');
        $results = $this->resource->getConnection()->query($sql);
        foreach ($results->fetchAll() as $rule) {
            if ($rule['rule_code'] == '') {
                $rule['rule_code'] = 'admin_transaction';
            }
            $this->mapRepository->createColumn([
                'name' => $rule['rule_code'],
                'data' => [
                    'expr'  => 'SUM(' . $rule['rule_code'] . ')',
                    'label' => ucwords(str_replace('_', ' ', $rule['rule_code'])),
                    'type'  => 'number',
                    'table' => $this->mapRepository->getTable('mst_rewards_points_aggregated_hour'),
                ],
            ]);
        }
    }

    /**
     * @param string $from
     * @return \Magento\Framework\DB\Select
     */
    private function getRuleCodeSql($from)
    {
        $connection = $this->resource->getConnection();
        $ruleCodeStatement = new \Zend_Db_Expr("SUBSTRING_INDEX(
                IF(
                    points_table.code REGEXP '.*import of transaction: [0-9]{1,} - .*',
                    SUBSTRING_INDEX(points_table.code, ' - ', -1),
                    points_table.code
                ),
                '-',
                1
            )");
        return $connection->select()
            ->from(['points_table' => $this->resource->getTableName('mst_rewards_transaction')], [
                'rule_code' => $ruleCodeStatement,
            ])
            ->where('points_table.created_at >= ?', $from)
            ->group('rule_code');
    }
}