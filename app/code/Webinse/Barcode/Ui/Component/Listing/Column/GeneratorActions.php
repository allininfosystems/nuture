<?php
/**
 * Webinse
 *
 * PHP Version 5.6.23
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */
/**
 * UI action list column
 *
 * @category    Webinse
 * @package     Webinse_Barcode
 * @author      Webinse Team <info@webinse.com>
 * @copyright   2017 Webinse Ltd. (https://www.webinse.com)
 * @license     http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0
 */

namespace Webinse\Barcode\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column as ParentColumn;
use Magento\Cms\Block\Adminhtml\Page\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

class GeneratorActions extends ParentColumn
{
    /** Url path */
    const BARCODE_GENERATOR_VIEW   = 'webinse_barcode/generator/view';
    const BARCODE_GENERATOR_PRINT  = 'webinse_barcode/generator/printBarcode';
    const BARCODE_GENERATOR_PRINT_LABEL  = 'webinse_barcode/generator/printLabelBarcode';
    const BARCODE_GENERATOR_EDIT   = 'webinse_barcode/generator/generate';
    const BARCODE_GENERATOR_DELETE = 'webinse_barcode/generator/delete';

    /** @var UrlBuilder */
    protected $actionUrlBuilder;

    /** @var UrlInterface */
    protected $urlBuilder;

    /** @var string */
    private $viewUrl;

    /** @var string */
    private $printUrl;

    /** @var string */
    private $printLabelUrl;

    /** @var string */
    private $editUrl;

    /**
     * GeneratorActions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     * @param string $viewUrl
     * @param string $printUrl
     * @param string $printLabelUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        $editUrl = self::BARCODE_GENERATOR_EDIT,
        $viewUrl = self::BARCODE_GENERATOR_VIEW,
        $printUrl = self::BARCODE_GENERATOR_PRINT,
        $printLabelUrl = self::BARCODE_GENERATOR_PRINT_LABEL
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->editUrl = $editUrl;
        $this->viewUrl = $viewUrl;
        $this->printUrl = $printUrl;
        $this->printLabelUrl = $printLabelUrl;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['entity_id'])) {
                    $item[$name]['view'] = [
                        'href' => $this->urlBuilder->getUrl($this->viewUrl, ['id' => $item['entity_id']]),
                        'label' => __('View')
                    ];
                    $item[$name]['print'] = [
                        'href' => $this->urlBuilder->getUrl($this->printUrl, ['id' => $item['entity_id']]),
                        'label' => __('Print Barcodes')
                    ];
                    $item[$name]['print_labels'] = [
                        'href' => $this->urlBuilder->getUrl($this->printLabelUrl, ['id' => $item['entity_id']]),
                        'label' => __('Print Labels')
                    ];
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->editUrl, ['id' => $item['entity_id']]),
                        'label' => __('Edit')
                    ];
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(self::BARCODE_GENERATOR_DELETE, ['entity_id' => $item['entity_id']]),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete ${ $.$data.barcode }'),
                            'message' => __('Are you sure you want to delete a barcode with number "${ $.$data.barcode }"?')
                        ]
                    ];
                }
            }
        }

        return $dataSource;
    }
}