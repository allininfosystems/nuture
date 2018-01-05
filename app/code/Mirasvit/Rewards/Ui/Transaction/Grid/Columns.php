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


namespace Mirasvit\Rewards\Ui\Transaction\Grid;

use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Customer\Api\Data\AttributeMetadataInterface as AttributeMetadata;

class Columns extends \Magento\Customer\Ui\Component\Listing\Columns
{
    /**
     * @param array $attributeData
     * @param string $columnName
     * @return bool
     */
    public function addColumn(array $attributeData, $columnName)
    {
        return true;
    }

}
