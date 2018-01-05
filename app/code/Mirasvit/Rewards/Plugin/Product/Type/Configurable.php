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


namespace Mirasvit\Rewards\Plugin\Product\Type;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable as ProductConfigurable;
use Magento\SalesRule\Model\Validator;
use Magento\Catalog\Model\ProductFactory;
use Mirasvit\Rewards\Helper\Output\Earn;
use Mirasvit\Rewards\Model\Config;

/**
 * @package Mirasvit\Rewards\Plugin
 */
class Configurable
{
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Json\DecoderInterface $jsonDecoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        Config $config,
        Earn $earnOutput,
        ProductFactory $productFactory
    ) {
        $this->registry       = $registry;
        $this->jsonDecoder    = $jsonDecoder;
        $this->jsonEncoder    = $jsonEncoder;
        $this->config         = $config;
        $this->earnOutput     = $earnOutput;
        $this->productFactory = $productFactory;
    }

    /**
     * @param ProductConfigurable $configurable
     * @param \callable     $proceed
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function aroundGetJsonConfig(ProductConfigurable $configurable, $proceed)
    {
        \Magento\Framework\Profiler::start(__CLASS__.'_default:'.__METHOD__);
        $returnValue = $proceed();
        if (!$this->registry->registry('current_product')) {
            \Magento\Framework\Profiler::stop(__CLASS__.'_default:'.__METHOD__);
            return $returnValue;
        }

        \Magento\Framework\Profiler::start(__CLASS__.':'.__METHOD__);
        $data = $this->jsonDecoder->decode($returnValue);

        foreach ($data['optionPrices'] as $productId => $prices) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->productFactory->create()->loadByAttribute('entity_id', $productId);
            if (!$product) {
                continue;
            }
            $data['optionPrices'][$productId]['rewardRules']['amount'] = $this->earnOutput
                ->getProductPoints($product);
            $data['prices']['rewardRules'] = [
                'amount' => $this->earnOutput->getProductPoints($product)
            ];
        }
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
        \Magento\Framework\Profiler::stop(__CLASS__.'_default:'.__METHOD__);

        return $this->jsonEncoder->encode($data);
    }
}