<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Shopbybrand\Block\Sidebar;

/**
 * Class BrandList
 *
 * @package Mageplaza\Shopbybrand\Block\Sidebar
 */
class BrandList extends \Mageplaza\Shopbybrand\Block\Brand
{
    /**
     * Default feature template
     *
     * @type string
     */
    protected $_template = 'sidebar/list.phtml';

    /*
     * Default title sidebar brand thumbnail
     */
    const TITLE = 'Brand List';

    /*
     * Default title sidebar brand thumbnail
     */
    const LIMIT = '7';

    //*********************** Get SideBar Brand Thumbnail Config ***********************
    /**
     * @return mixed
     */
    public function getTitle()
    {
        $title = ($this->helper->getShopByBrandConfig(
            'sidebar/brand_thumbnail/title'
        )) ? $this->helper->getShopByBrandConfig(
            'sidebar/brand_thumbnail/title'
        ) : SELF::TITLE;
        return $title;
    }

    /**
     * @return int|mixed
     */
    public function getLimit()
    {
        //Get limit brand config
        $limit = ($this->helper->getShopByBrandConfig(
            'sidebar/brand_thumbnail/limit_brands'
        )) ? $this->helper->getShopByBrandConfig(
            'sidebar/brand_thumbnail/limit_brands'
        ) : SELF::LIMIT;
        //Get number brand collection
        $collectionSize = count($this->getCollection());
        $result = ($limit < $collectionSize) ? $limit : (string)$collectionSize;
        return $this->toString($result);
    }

    //*********************** Get Brand Informations ***********************
    /**
     * @param $brand
     *
     * @return string
     */
    public function getBrandUrl($brand = null)
    {
        return $this->helper->getBrandUrl($brand);
    }

    /**
     * @param $brand
     *
     * @return string
     */
    public function getBrandImageUrl($brand)
    {
        return $this->helper->getBrandImageUrl($brand);
    }

}
