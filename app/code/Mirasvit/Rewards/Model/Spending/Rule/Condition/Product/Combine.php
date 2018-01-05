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


namespace Mirasvit\Rewards\Model\Spending\Rule\Condition\Product;

use Magento\Catalog\Model\ResourceModel\Product\Collection;

class Combine extends \Magento\SalesRule\Model\Rule\Condition\Product\Combine
{
    /**
     * @var \Magento\SalesRule\Model\Rule\Condition\Product
     */
    protected $_ruleConditionProd;

    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\SalesRule\Model\Rule\Condition\Product $ruleConditionProduct,
        array $data = []
    ) {
        parent::__construct($context, $ruleConditionProduct, $data);

        $this->setType('Mirasvit\Rewards\Model\Spending\Rule\Condition\Product\Combine');
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $conditions = parent::getNewChildSelectOptions();

        $attributes = [];
        $attributes[] = [
            'value' => 'Mirasvit\Rewards\Model\Spending\Rule\Condition\CustomProductAttributes|price',
            'label' => __('Base Price'),
        ];
        $attributes[] = [
            'value' => 'Mirasvit\Rewards\Model\Spending\Rule\Condition\CustomProductAttributes|final_price',
            'label' => __('Final Price'),
        ];
        $attributes[] = [
            'value' => 'Mirasvit\Rewards\Model\Spending\Rule\Condition\CustomProductAttributes|special_price',
            'label' => __('Special Price'),
        ];

        $conditions = array_merge_recursive(
            $conditions,
            [
                ['label' => __('Custom Product Attributes'), 'value' => $attributes]
            ]
        );

        return $conditions;
    }
}
