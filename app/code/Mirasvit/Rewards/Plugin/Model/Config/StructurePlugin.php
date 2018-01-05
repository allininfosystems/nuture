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


namespace Mirasvit\Rewards\Plugin\Model\Config;

use Magento\Config\Model\Config\Structure;
use Magento\Framework\App\ResourceConnection;

class StructurePlugin
{
    /**
     * @var \Magento\Config\Model\Config\ScopeDefiner
     */
    private $scopeDefiner;

    public function __construct(
        \Magento\Config\Model\Config\ScopeDefiner $scopeDefiner
    ) {
        $this->scopeDefiner = $scopeDefiner;
    }

    /**
     * @param Structure $subject
     * @param \Closure  $proceed
     * @param array     $pathParts
     * @return \Magento\Config\Model\Config\Structure\ElementInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetElementByPathParts(Structure $subject, \Closure $proceed, array $pathParts)
    {
        $result = $proceed($pathParts);

        if ($pathParts == ['sales']) {
            $data = $result->getData();
            $rewardsField = $data['children']['totals_sort']['children']['subtotal'];

            $rewardsField['id']        = 'rewards_total';
            $rewardsField['sortOrder'] = 50;
            $rewardsField['label']     = 'Rewards Earn';
            $data['children']['totals_sort']['children']['rewards_total'] = $rewardsField;

            $rewardsField['id']        = 'rewards_spend';
            $rewardsField['sortOrder'] = 60;
            $rewardsField['label']     = 'Rewards Spend';
            $data['children']['totals_sort']['children']['rewards_spend'] = $rewardsField;

            $result->setData($data, $this->scopeDefiner->getScope());
        }

        return $result;
    }
}