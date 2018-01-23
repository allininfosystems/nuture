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
namespace  Magenest\ZohocrmIntegration\Block\Adminhtml\Map;

use Magento\Backend\Block\Template;

/**
 * Class UpdateFields
 *
 * @package Magenest\ZohocrmIntegration\Block\Adminhtml\Map
 */
class UpdateFields extends Template
{
    /**
     * Get Url retrieve
     *
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('zohocrm/field/retrieve', ['_current' => false]);
    }

    /**
     * Get Url Update All Fields
     *
     * @return string
     */
    public function getUpdateAllFields()
    {
        return $this->getUrl('zohocrm/field/update', ['_current' => false]);
    }
}
