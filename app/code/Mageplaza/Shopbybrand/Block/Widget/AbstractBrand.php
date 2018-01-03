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
namespace Mageplaza\Shopbybrand\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Shopbybrand\Helper\Data as Helper;
use Magento\Widget\Block\BlockInterface;

/**
 * Class AbstractBrand
 *
 * @package Mageplaza\Shopbybrand\Block\Brand
 */
class AbstractBrand extends Template implements BlockInterface
{
    /**
     * @type \Mageplaza\Shopbybrand\Helper\Data
     */
    protected $helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Mageplaza\Shopbybrand\Helper\Data               $helper
     * @param \Mageplaza\Shopbybrand\Model\BrandFactory        $brandFactory
     */
    public function __construct(Context $context, Helper $helper)
    {
        $this->helper = $helper;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    function includeCssLib()
    {
        $cssFiles = ['Mageplaza_Core::css/owl.carousel.css', 'Mageplaza_Core::css/owl.theme.css'];
        $template = '<link rel="stylesheet" type="text/css" media="all" href="%s">' . "\n";
        $result = '';
        foreach ($cssFiles as $file) {
            $asset = $this->_assetRepo->createAsset($file);
            $result .= sprintf($template, $asset->getUrl());
        }
        return $result;
    }

    /**
     * @return \Mageplaza\Shopbybrand\Helper\Data
     */
    public function helper()
    {
        return $this->helper;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->getData('title');
    }

}
