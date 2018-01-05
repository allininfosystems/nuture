<?php


/**
 * webideaonline.com.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://webideaonline.com/licensing/
 *
 */

namespace WIO\Forum\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class BlockActions
 */
class PostActions extends Column {
  /**
   * Url path
   */
  const URL_PATH_EDIT = 'wio_forum/post/edit';
  const URL_PATH_DELETE = 'wio_forum/post/delete';
  const URL_PATH_DETAILS = 'wio_forum/post/details';

  /**
   * @var UrlInterface
   */
  protected $urlBuilder;

  /**
   * Constructor
   *
   * @param ContextInterface $context
   * @param UiComponentFactory $uiComponentFactory
   * @param UrlInterface $urlBuilder
   * @param array $components
   * @param array $data
   */
  public function __construct(
  ContextInterface $context, UiComponentFactory $uiComponentFactory, UrlInterface $urlBuilder, array $components = [], array $data = []
  ) {
    $this->urlBuilder = $urlBuilder;
    parent::__construct($context, $uiComponentFactory, $components, $data);
  }

  /**
   * @param array $items
   * @return array
   */

  /**
   * Prepare Data Source
   *
   * @param array $dataSource
   * @return array
   */
  public function prepareDataSource(array $dataSource) {
    if (isset($dataSource['data']['items'])) {
      foreach ($dataSource['data']['items'] as & $item) {
        if (isset($item['post_id'])) {
          $item[$this->getData('name')] = [
              'edit' => [
                  'href' => $this->urlBuilder->getUrl(
                          static::URL_PATH_EDIT, [
                      'post_id' => $item['post_id']
                          ]
                  ),
                  'label' => __('Edit')
              ],
              'delete' => [
                  'href' => $this->urlBuilder->getUrl(
                          static::URL_PATH_DELETE, [
                      'post_id' => $item['post_id']
                          ]
                  ),
                  'label' => __('Delete'),
                  'confirm' => [
                      'title' => __('Delete Post'),
                      'message' => __('Are you sure you wan\'t to delete a post record?')
                  ]
              ]
          ];
        }
      }
    }

    return $dataSource;
  }

}
