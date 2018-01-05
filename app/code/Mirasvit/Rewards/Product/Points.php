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



namespace Mirasvit\Rewards\Product;

class Points
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @param \Magento\Review\Block\Product\ReviewRenderer $reviewRenderer
     * @param \Magento\Catalog\Model\Product               $product
     * @param bool                                         $templateType
     * @param bool                                         $displayIfNoReviews
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetReviewsSummaryHtml(
        \Magento\Review\Block\Product\ReviewRenderer $reviewRenderer,
        \Magento\Catalog\Model\Product $product,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        $this->product = $product;

        return [
            $product,
            $templateType,
            $displayIfNoReviews
        ];
    }
    /**
     * Get product reviews summary
     *
     * @param \Magento\Review\Block\Product\ReviewRenderer $reviewRenderer
     * @param string                                       $result
     * @return string
     */
    public function afterGetReviewsSummaryHtml(
        \Magento\Review\Block\Product\ReviewRenderer $reviewRenderer,
        $result
    ) {
        if (!$reviewRenderer->getLayout()->getBlock('rewards.product.points')) {
            $result .= $reviewRenderer->getLayout()
                ->createBlock('\Mirasvit\Rewards\Block\Product\Points')
                ->setProduct($this->product)
                ->toHtml();
        }

        return $result;
    }
}
