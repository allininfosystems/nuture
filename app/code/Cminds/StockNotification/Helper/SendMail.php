<?php

namespace Cminds\StockNotification\Helper;

use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

class SendMail extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductFactory
     */
    private $product;

    /**
     * Current product
     *
     * @var object
     */
    private $currentProduct;

    /**
     * SendMail constructor
     *
     * @param Context               $context
     * @param TransportBuilder      $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param ProductFactory        $product
     */
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        ProductFactory $product
    ) {

        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->product = $product;

        parent::__construct($context);
    }

    /**
     * Send email with notification to the given user e-mail
     *
     * @param string $email
     * @param int    $productId
     *
     * @return bool
     * @throws \Magento\Framework\Exception\MailException
     */
    public function send($email, $productId)
    {
        $this->currentProduct = $this->product->create()->load($productId);

        $store = $this->storeManager->getDefaultStoreView()->getId();
        $transport = $this->transportBuilder->setTemplateIdentifier('stocknotification_instock_template')
            ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
            ->setTemplateVars(
                [
                    'store' => $this->storeManager->getStore(),
                    'product_name' => $this->currentProduct->getName(),
                    'product_url' => $this->currentProduct->setStoreId($store)->getUrlInStore(),
                    'subject' => __(
                        'Product %1 is back in stock!',
                        $this->currentProduct->getName()
                    ),
                ]
            )
            ->setFrom('general')
            ->addTo($email)
            ->getTransport();
        $transport->sendMessage();

        return true;
    }
}
