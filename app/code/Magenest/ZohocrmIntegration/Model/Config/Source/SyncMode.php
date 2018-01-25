<?php
/**
 * Created by PhpStorm.
 * User: canhnd
 * Date: 09/02/2017
 * Time: 10:37
 */
namespace Magenest\ZohocrmIntegration\Model\Config\Source;

/**
 * Class SyncMode
 * @package Magenest\ZohocrmIntegration\Model\Config\Source
 */
class SyncMode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options array
     *
     * @var array
     */
    protected $_options = [ 1 => 'Add to Queue', 2 => 'Auto Sync'];

    /**
     * Return options array
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->_options;
        return $options;
    }
}
