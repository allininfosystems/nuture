<?php
/**
 * Created by PhpStorm.
 * User: canhnd
 * Date: 09/02/2017
 * Time: 14:06
 */
namespace Magenest\ZohocrmIntegration\Model\ResourceModel\Queue;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magenest\ZohocrmIntegration\Model\Queue', 'Magenest\ZohocrmIntegration\Model\ResourceModel\Queue');
    }
}
