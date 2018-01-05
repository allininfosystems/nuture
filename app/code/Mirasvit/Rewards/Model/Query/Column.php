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


namespace Mirasvit\Rewards\Model\Query;

use Magento\Framework\Api\FilterBuilder;
use Mirasvit\Report\Api\Repository\MapRepositoryInterface;
use Mirasvit\Rewards\Api\Data\Query\ColumnInterface;

class Column extends \Mirasvit\Report\Model\Query\Column implements ColumnInterface
{
    public function __construct(
        FilterBuilder $filterBuilder,
        MapRepositoryInterface $mapRepository,
        $name,
        $data = []
    ) {
        parent::__construct($filterBuilder, $mapRepository, $name, $data);

        $this->isHidden   = isset($data['isHidden']) ? $data['isHidden'] : '';
        $this->isDisabled = isset($data['isDisabled']) ? $data['isDisabled'] : '';
    }

    /**
     * {@inheritdoc}
     */
    public function getIsHidden()
    {
        return $this->isHidden;
    }

    /**
     * {@inheritdoc}
     */
    public function getIsDisabled()
    {
        return $this->isDisabled;
    }
}
