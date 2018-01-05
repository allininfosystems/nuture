<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   2.1.6
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Rewards\Pricing\Render;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Pricing\Render\AbstractAdjustment;
use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Catalog\Helper\Data as CatalogData;
use Magento\Catalog\Pricing\Price\CustomOptionPrice;
use Magento\Weee\Helper\Data as WeeData;

use Mirasvit\Rewards\Model\Config;
use Mirasvit\Rewards\Helper\Output\Spend;
use \Mirasvit\Rewards\Helper\Output\Earn;
use Mirasvit\Rewards\Helper\Data;

/**
 * @method string getIdSuffix()
 * @method string getDisplayLabel()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Adjustment extends AbstractAdjustment
{
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Config                 $config,
        Earn                   $earnOutput,
        Spend                  $spendOutput,
        Data                   $rewardsDataHelper,
        CatalogData            $catalogData,
        WeeData                $weeData,
        Session                $customerSession,
        PriceCurrencyInterface $priceCurrency,
        Template\Context       $context,
        array                  $data = []
    ) {
        $this->earnOutput        = $earnOutput;
        $this->spendOutput       = $spendOutput;
        $this->rewardsDataHelper = $rewardsDataHelper;
        $this->catalogData       = $catalogData;
        $this->weeData           = $weeData;
        $this->customerSession   = $customerSession;
        $this->config            = $config;
        parent::__construct($context, $priceCurrency, $data);
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    protected function getProduct()
    {
        return $this->getSaleableItem();
    }

    /**
     * {@inheritdoc}
     */
    protected function apply()
    {
        return $this->toHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentCode()
    {
        return \Mirasvit\Rewards\Pricing\Adjustment::ADJUSTMENT_CODE;
    }

    /**
     * Define if both prices should be displayed
     *
     * @return bool
     */
    public function isShowPoints()
    {
        \Magento\Framework\Profiler::start(__METHOD__);
        if ($this->isBundle() && $this->isProductPage()) {
            global $i;
            if (!$i) {
                $i = 0;
            }
            $i ++;
            if ($i == 2) { //need to find better way to show block only once on the bundle product page.
                \Magento\Framework\Profiler::stop(__METHOD__);
                return false;
            }
        }

        if ($this->isProductPage()) {
            $isAllowToShow = $this->config->getDisplayOptionsIsShowPointsOnProductPage();
        } else {
            $isAllowToShow = $this->config->getDisplayOptionsIsShowPointsOnFrontend();
        }

        $f = $isAllowToShow && $this->getPointsFormatted() && !$this->isOptionPrice();
        \Magento\Framework\Profiler::stop(__METHOD__);
        return $f;
    }

    /**
     * @return bool
     */
    public function isOptionPrice()
    {
        return $this->getAmountRender()->getPrice()->getPriceCode() == CustomOptionPrice::PRICE_CODE;
    }

    /**
     * @return int
     */
    public function getCurrentPoints()
    {
        $n = $this->earnOutput->getProductPoints($this->getProduct());
        return $n;
    }

    //    /**
    //     * @return int
    //     */
    //    public function getMaxPointsForConfigurableProduct()
    //    {
    //        if ($this->getSaleableItem()->getTypeId() != Configurable::TYPE_CODE) {
    //            return;
    //        }
    //        $max  = [0];
    //        $item = $this->getSaleableItem();
    //
    //        $products = $item->getTypeInstance()->getUsedProducts($item);
    //
    //        foreach ($products as $product) {
    //            $max[] = $this->earnOutput->getProductPoints(
    //                $product
    //            );
    //        }
    //        return max($max);
    //    }

    /**
     * @return bool
     */
    private function isBundle()
    {
        return $this->getSaleableItem()->getTypeId() == \Magento\Bundle\Model\Product\Type::TYPE_CODE;
    }

    /**
     * @return bool
     */
//    private function isConfigurable()
//    {
//        return $this->getSaleableItem()->getTypeId() == Configurable::TYPE_CODE;
//    }


    /**
     * @return string|bool
     */
    public function getPointsFormatted()
    {
        \Magento\Framework\Profiler::start(__METHOD__);

        $points = $this->earnOutput->getProductPoints($this->getProduct());
        if (!$points) {
            \Magento\Framework\Profiler::start(__METHOD__);

            return false;
        }

        $money = $this->spendOutput->getProductPointsAsMoney($points, $this->getProduct());
        if ($points != $money) {
            \Magento\Framework\Profiler::start(__METHOD__);

            return __("Possible discount %1 %2", $this->getLabel(), $money);
        }

        $label = __('Earn %1 %2', $this->getLabel(), $this->rewardsDataHelper->formatPoints($points));
        //if ($this->isConfigurable()) {
        //    $label = __('Earn up to %1 %2', $this->getLabel(), $this->rewardsDataHelper->formatPoints($points));
        //}
        if ($this->isBundle()) {
            $label = __('Earn at least %1 %2', $this->getLabel(), $this->rewardsDataHelper->formatPoints($points));
        }

        \Magento\Framework\Profiler::start(__METHOD__);

        return $label;
    }

    /**
     * Build identifier with prefix
     *
     * @param string $prefix
     * @return string
     */
    public function buildIdWithPrefix($prefix)
    {
        $priceId = $this->getPriceId();
        if (!$priceId) {
            $priceId = $this->getSaleableItem()->getId();
        }
        return $prefix . $priceId . $this->getIdSuffix();
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        $label = '';
        if (!$this->getAmountRender()) {
            return $label;
        }

        switch ($this->getAmountRender()->getTypeId()) {
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                $label = 'starting at';
                break;
            case \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE:
                $label = 'Up to';
                break;
        }

        return $label;
    }

    /**
     * @return bool
     */
    private function isProductPage()
    {
        return !$this->getData('list_category_page') ||
            $this->getData('zone') == \Magento\Framework\Pricing\Render::ZONE_ITEM_VIEW;
    }
}
