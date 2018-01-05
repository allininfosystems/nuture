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


namespace Mirasvit\Rewards\Model\Spending\Rule\Condition;

class CustomProductAttributes extends \Magento\SalesRule\Model\Rule\Condition\Product
{

    /**
     * {@inheritdoc}
     */
    protected function _addSpecialAttributes(array &$attributes)
    {
        parent::_addSpecialAttributes($attributes);

        $attributes = array_merge($attributes, [
            'price'            => __('Base Price'),
            'final_price'      => __('Final Price'),
            'special_price'    => __('Special Price'),
        ]);
    }
    public function validate(\Magento\Framework\Model\AbstractModel $model)
    {
        $attrCode = $this->getAttribute();

        switch ($attrCode) {
            case 'price':
                return $this->validateAttribute((float)$model->getProduct()->getPrice());
                break;

            case 'final_price':
                return $this->validateAttribute((float)$model->getProduct()->getFinalPrice());
                break;

            case 'special_price':
                return $this->validateAttribute((float)$model->getProduct()->getSpecialPrice());
                break;
        }

        return parent::validate($model);
    }
}
