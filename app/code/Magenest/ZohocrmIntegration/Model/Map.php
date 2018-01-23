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
namespace Magenest\ZohocrmIntegration\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * Class Map
 *
 * @package Magenest\ZohocrmIntegration\Model
 */
class Map extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'map';

    /**
     *
     */
    public function _construct()
    {
        $this->_init('Magenest\ZohocrmIntegration\Model\ResourceModel\Map');
    }
}
