<?php
namespace Webinse\Barcode\Plugin;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\UrlInterface;

class ProductActions
{
    protected $context;
    protected $urlBuilder;
    public function __construct(
        ContextInterface $context,
        UrlInterface $urlBuilder
    )
    {
        $this->context = $context;
        $this->urlBuilder = $urlBuilder;
    }
    public function afterPrepareDataSource(
        \Magento\Catalog\Ui\Component\Listing\Columns\ProductActions $subject,
        array $dataSource
    ) {
        if (isset($dataSource['data']['items'])) {
            $storeId = $this->context->getFilterParam('store_id');

            foreach ($dataSource['data']['items'] as &$item) {
                $item[$subject->getData('name')]['generate_barcode'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'webinse_barcode/generator/generate',
                        ['id' => $item['entity_id'], 'product' => $item['entity_id'], 'store' => $storeId]
                    ),
                    'label' => __('Generate Barcode'),
                    'hidden' => false,
                ];
                $item[$subject->getData('name')]['print_barcode'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'webinse_barcode/generator/printBarcode',
                        ['id' => $item['entity_id'], 'product' => $item['entity_id'], 'store' => $storeId]
                    ),
                    'label' => __('Print Barcode'),
                    'hidden' => false,
                ];
            }
        }

        return $dataSource;
    }
}