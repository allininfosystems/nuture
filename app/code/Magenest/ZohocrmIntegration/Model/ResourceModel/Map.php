<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ZohocrmIntegration extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ZohocrmIntegration
 * @author   ThaoPV
 */
namespace Magenest\ZohocrmIntegration\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Map
 *
 * @package Magenest\ZohocrmIntegration\Model\ResourceModel
 */
class Map extends AbstractDb
{
    /**
     * Set main entity table name and primary key field name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magenest_zohocrm_map', 'id');
    }
}
