<?php
/**
 * Created by PhpStorm.
 * User: canhnd
 * Date: 13/02/2017
 * Time: 10:49
 */
namespace Magenest\ZohocrmIntegration\Model\Config\Source;

/**
 * Class Edition
 * @package Magenest\ZohocrmIntegration\Model\Config\Source
 */
class Edition implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options array
     *
     * @var array
     */
    protected $_options = [
        0 => 'Select Edition',
        1000 => 'Free Edition',
        5000 => 'Standard Edition',
        10000 => 'Professional Edition',
        25000 => 'Enterprise Edition'
    ];

    /**
     * Return options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->_options;
        return $options;
    }
}
