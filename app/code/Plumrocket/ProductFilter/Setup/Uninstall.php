<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Product Filter v3.x.x
 * @copyright   Copyright (c) 2016 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ProductFilter\Setup;

/* Uninstall Product Filter */
class Uninstall extends \Plumrocket\Base\Setup\AbstractUninstall
{
    /**
     * Config section id
     * @var string
     */
    protected $_configSectionId = 'prproductfilter';

    /**
     * Pathes to files
     * @var Array
     */
    protected $_pathes = ['/app/code/Plumrocket/ProductFilter'];

    /**
     * Attributes
     * @var Array
     */
    protected $_attributes = [
      \Magento\Catalog\Model\Category::ENTITY => ['pr_exluded_attributes']
     ];
}
