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


namespace Mirasvit\Rewards\Plugin\Product;

use Magento\Catalog\Block\Product\View as ProductView;
use Magento\SalesRule\Model\Validator;
use Magento\Catalog\Model\ProductFactory;
use Mirasvit\Rewards\Helper\Output\Earn;
use Mirasvit\Rewards\Model\Config;

/**
 * @package Mirasvit\Rewards\Plugin
 */
class View
{
    public function __construct(
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        Config $config,
        Earn $earnOutput,
        ProductFactory $productFactory
    ) {
        $this->jsonDecoder    = $jsonDecoder;
        $this->jsonEncoder    = $jsonEncoder;
        $this->config         = $config;
        $this->earnOutput     = $earnOutput;
        $this->productFactory = $productFactory;
    }

    /**
     * @param ProductView $view
     * @param \callable   $proceed
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundGetJsonConfig(ProductView $view, $proceed)
    {
        $returnValue = $proceed();

        $data = $this->jsonDecoder->decode($returnValue);

        $data['prices']['rewardRules'] = [
            'adjustments' => [],
            'amount'      => $this->earnOutput->getProductPoints($view->getProduct())
        ];
        if (!empty($data['optionTemplate'])) {
            $unit = $this->config->getGeneralPointUnitName();
            $unit = str_replace(['(', ')'], '', $unit);
            $data['optionTemplate'] .= ' <% if (data.Rewards.value) { %>, '.
                '<%= data.Rewards.value %>'.
                '<% } %> '.
                $unit;
        }

        return $this->jsonEncoder->encode($data);
    }
}