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



namespace Mirasvit\Rewards\Model\Rewrite;

use Mirasvit\Rewards\Model\Config as Config;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Sendfriend extends \Magento\SendFriend\Model\SendFriend
{
    /**
     * @var \Mirasvit\Rewards\Helper\Behavior
     */
    protected $rewardsBehavior;

    /**
     * @var \Magento\Framework\Model\Context
     */
    protected $context;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb
     */
    protected $resourceCollection;

    /**
     * @param \Mirasvit\Rewards\Helper\Behavior                            $rewardsBehavior
     * @param \Magento\Framework\Model\Context                             $context
     * @param \Magento\Framework\Registry                                  $registry
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager
     * @param \Magento\Framework\Mail\Template\TransportBuilder            $transportBuilder
     * @param \Magento\Catalog\Helper\Image                                $catalogImage
     * @param \Magento\SendFriend\Helper\Data                              $sendfriendData
     * @param \Magento\Framework\Escaper                                   $escaper
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress         $remoteAddress
     * @param \Magento\Framework\Stdlib\CookieManagerInterface             $cookieManager
     * @param \Magento\Framework\Translate\Inline\StateInterface           $inlineTranslation
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null           $resourceCollection
     * @param array                                                        $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Behavior $rewardsBehavior,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Catalog\Helper\Image $catalogImage,
        \Magento\SendFriend\Helper\Data $sendfriendData,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->rewardsBehavior = $rewardsBehavior;
        $this->context = $context;
        $this->registry = $registry;
        $this->resource = $resource;
        $this->resourceCollection = $resourceCollection;
        parent::__construct(
            $context,
            $registry,
            $storeManager,
            $transportBuilder,
            $catalogImage,
            $sendfriendData,
            $escaper,
            $remoteAddress,
            $cookieManager,
            $inlineTranslation,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function send()
    {
        parent::send();
        $product = $this->getProduct();
        $this->rewardsBehavior->processRule(Config::BEHAVIOR_TRIGGER_SEND_LINK,
            false, false, $product->getId());
    }
}
