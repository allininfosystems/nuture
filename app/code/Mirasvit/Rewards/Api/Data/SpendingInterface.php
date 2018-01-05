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


namespace Mirasvit\Rewards\Api\Data;

interface SpendingInterface
{
    const ID = 'spending_rule_id';
    const NAME = 'name';
    const DESCRIPTION = 'description';
    const IS_ACTIVE = 'is_active';
    const ACTIVE_FROM = 'active_from';
    const TYPE = 'type';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const ACTIONS_SERIALIZED = 'actions_serialized';
    const SPENDING_STYLE = 'spending_style';
    const SPEND_POINTS = 'spend_points';
    const MONETARY_STEP = 'monetary_step';
    const SPEND_MIN_POINTS = 'spend_min_points';
    const SPEND_MAX_POINTS = 'spend_max_points';
    const SORT_ORDER = 'sort_order';
    const IS_STOP_PROCESSING = 'is_stop_processing';
    const WEBSITE_IDS = 'website_ids';
    const CUSTOMER_GROUP_IDS = 'customer_group_ids';


}
