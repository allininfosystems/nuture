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
namespace Magenest\ZohocrmIntegration\Model\ResourceModel\Map;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection Map
 *
 * @package Magenest\ZohocrmIntegration\Model\ResourceModel\Map
 */
class Collection extends AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'magenest_zohocrm_map_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'map_collection';


    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\ZohocrmIntegration\Model\Map', 'Magenest\ZohocrmIntegration\Model\ResourceModel\Map');
    }//end _construct()
}//end class
