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
 * Class BrandCategory
 *
 * @package Mageplaza\Shopbybrand\Block\Sidebar
 */
class BrandCategory extends \Mageplaza\Shopbybrand\Block\Brand
{
    /**
     * Default category template
     *
     * @type string
     */
    protected $_template = 'sidebar/category.phtml';

    /*
    * Default title sidebar category brand
    */
    const TITLE = 'Brand Category';

    /*
     * Default title sidebar category brand
     */
    const LIMIT = '7';
    //*********************** Get Category Collection ***********************

    public function getCatUrl($cat)
    {
        return $this->helper()->getCatUrl($cat);
    }

    //*********************** Get SideBar Brand Category Config ***********************
    /**
     * @return mixed
     */
    public function getTitle()
    {
        $title = ($this->helper->getShopByBrandConfig(
            'sidebar/category_brand/title'
        )) ? $this->helper->getShopByBrandConfig(
            'sidebar/category_brand/title'
        ) : SELF::TITLE;
        return $title;
    }

    /**
     * @return int|mixed
     */
    public function getLimit()
    {
        //Get limit category config
        $limit = ($this->helper->getShopByBrandConfig(
            'sidebar/category_brand/limit_categories'
        )) ? $this->helper->getShopByBrandConfig(
            'sidebar/category_brand/limit_categories'
        ) : self::LIMIT;
        //Get number category collection
        $collectionSize = count($this->getCategories()->getData());
        $result = ($limit < $collectionSize) ? $limit : (string)$collectionSize;
        return $this->toString($result);
    }

    /**
     * @return mixed
     */
    public function showBrandQty()
    {
        return $this->helper->getShopByBrandConfig(
            'sidebar/category_brand/show_brand_qty'
        );
    }
}
