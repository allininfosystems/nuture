<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket Product Filter v3.x.x
 * @copyright   Copyright (c) 2017 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\ProductFilter\Controller;

use \Plumrocket\ProductFilter\Helper\Url as UrlHelper;
use \Magento\Framework\App\ActionInterface as ActionInterface;

class Router implements \Magento\Framework\App\RouterInterface
{

    const ATTRIBUTES_CACHE_INDEFINER = 'product_filter_attribute_cache';

    /**
     * Catalog search identificator
     * @var string
     */
    protected $_catalogSearchInd = 'catalogsearch/result';

    /**
     * Data helper
     * @var \Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * Filtrable attributes
     * @var array
     */
    protected $_filterableAttributes;

    /**
     * Filter list
     * @var \Plumrocket\ProductFilter\Model\FilterList
     */
    protected $_filterList;

    /**
     * Cache
     * @var \Magento\Framework\Acl\Cache
     */
    protected $_cache;

    /**
     * Json helper
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $_jsonHelper;

    /**
     * Url helper
     * @var \Plumrocket\ProductFilter\Helper\Url
     */
    protected $_urlHelper;

    /**
     * AttotinalFilters
     * @var \Plumrocket\ProductFilter\Model\Config\Source\AdditionalFilters
     */
    protected $_additionalFilters;

    /**
     * Category factory
     * @var \Magento\Catalog\Model\Category
     */
    protected $_category;

    /**
     * Toolbar variables
     * @var array
     */
    protected $_toolbarVars = [];

    /**
     * Url Builder
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Response Interface
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $response;

    /**
     * ActionFlag
     * @var \Magento\Framework\App\ActionFlag
     */
    protected $actionFlag;

    /**
     * Constructor.
     * @param \Plumrocket\ProductFilter\Helper\Data                           $dataHelper
     * @param UrlHelper                                                       $urlHelper
     * @param \Plumrocket\ProductFilter\Model\FilterList                      $filterList
     * @param \Magento\Framework\App\Cache                                    $cache
     * @param \Magento\Framework\Json\Helper\Data                             $jsonHelper
     * @param \Magento\Catalog\Model\CategoryFactory                          $categoryFactory
     * @param \Plumrocket\ProductFilter\Model\Config\Source\AdditionalFilters $additionalFilters
     * @param \Magento\Framework\UrlInterface                                 $urlBuilder
     * @param \Magento\Framework\App\ResponseInterface                        $response
     * @param \Magento\Framework\App\ActionFlag                               $actionFlag
     */
    public function __construct(
        \Plumrocket\ProductFilter\Helper\Data                           $dataHelper,
        UrlHelper                                                       $urlHelper,
        \Plumrocket\ProductFilter\Model\FilterList                      $filterList,
        \Magento\Framework\App\Cache                                    $cache,
        \Magento\Framework\Json\Helper\Data                             $jsonHelper,
        \Magento\Catalog\Model\CategoryFactory                          $categoryFactory,
        \Plumrocket\ProductFilter\Model\Config\Source\AdditionalFilters $additionalFilters,
        \Magento\Framework\UrlInterface                                 $urlBuilder,
        \Magento\Framework\App\ResponseInterface                        $response,
        \Magento\Framework\App\ActionFlag                               $actionFlag
    ) {
        $this->_dataHelper          = $dataHelper;
        $this->_filterList          = $filterList;
        $this->_cache               = $cache;
        $this->_urlHelper           = $urlHelper;
        $this->_jsonHelper          = $jsonHelper;
        $this->_additionalFilters   = $additionalFilters;
        $this->_category            = $categoryFactory->create();
        $this->_toolbarVars         = $urlHelper->getToolbarVars();
        $this->urlBuilder           = $urlBuilder;
        $this->response             = $response;
        $this->actionFlag           = $actionFlag;
    }

    /**
     * {@inheritdoc}
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if ($this->_urlHelper->useSeoFriendlyUrl()) {

            $identifier = trim($request->getPathInfo(), '/');
            $sufix = $this->_urlHelper->getCategoryUrlSufix();
            $identifier = str_replace($sufix, '', $identifier);

            //Explode path by slashes and going from the end
            // Search attributes, which must be converted to params
            $parts = explode('/', $identifier);
            $_params = [];

            $sourceFilterParts = [];

            foreach ($parts as $kP => $part) {
                // It attribute only if has separator, which separate attribute var and value
                if (mb_strpos($part, $this->_urlHelper->getFilterParamSeparator()) === false) {
                    continue;
                }

                list($code, $value) = explode($this->_urlHelper->getFilterParamSeparator(), $part);
                $value = htmlentities(urldecode($value));
                $additionalFilters = $this->_additionalFilters->toArray();

                //Checking can is attribute of product filter
                //If filter dont know this attribute, than skip it
                if ($this->_canRewrite($code)) {

                    if (!isset($additionalFilters[$code])) {
                        if (in_array($code, $this->_dataHelper->getSelectedAttributeCodes())
                            || $code == $this->getCategorVar()
                        ) {
                            // Fix for price
                            if ($code == 'price') {
                                $value = str_replace('_', '-', $value);
                            } else {
                                $value = $this->_getAttributeValueId($code, $value);
                            }
                        }
                    } elseif ($code == \Plumrocket\ProductFilter\Model\Layer\Filter\StockStatus::FILTER_REQUEST_VAR) {
                        //Stock status fixes
                        if ($value == $this->_dataHelper->getConvertedAttributeValue(__('In Stock'))) {
                            $value = 1;
                        } else {
                            $value = 0;
                        }
                    }
                    $sourceFilterParts[] = $part;
                    $_params[$code][] = $value;
                    unset($parts[$kP]);
                }
            }

            // Checks if filter params is sorted
            // If necessary, sorts the parameters and set redirect 301
            if ($sourceFilterParts) {
                $sortedFilterParts = $sourceFilterParts;
                if (sort($sortedFilterParts) && $sourceFilterParts !== $sortedFilterParts) {
                    $notSortedStr   = implode('/', $sourceFilterParts);
                    $sortedStr      = implode('/', $sortedFilterParts);

                    $newUrl = str_replace($notSortedStr, $sortedStr, $this->urlBuilder->getCurrentUrl());
                    $this->makeRedirect($newUrl);
                    return;
                }
            }

            $newPath = implode('/', $parts);
            if (!empty($_params)) {
                foreach ($_params as $code => $value) {
                    $_value = implode(',', $value);
                    $request->setParam($code, $_value);
                }
                $request->setPathInfo($newPath . $sufix);
            }

            // Fix for search page
            // If its search page and there no action name in path
            // Then add controller\action name (index/index) to path
            if ($this->_catalogSearchInd == $newPath) {
                $newPath .= '/index/index';
                $request->setPathInfo($newPath . $sufix);
            }
        } else {
            // get filter params
            $params = $request->getParams();
            $anotherParam = [];
            foreach ($params as $code => $value) {
                if ($this->_canRewrite($code)) {
                    $params[$code] = explode(',', $value);
                } else {
                    $anotherParam[$code] = $value;
                    unset($params[$code]);
                }
            }

            // sort filter params
            $sortedParams = $params;
            ksort($sortedParams, SORT_DESC);
            foreach ($sortedParams as $code => $values) {
                sort($values);
                $sortedParams[$code] = $values;
            }

            // Checks if filter params is sorted
            // If necessary, sorts the parameters and set redirect 301
            if ($sortedParams !== $params) {
                $sortedParamsStr    = $this->createUrlParams($sortedParams);
                $paramsStr          = $this->createUrlParams($params);

                if (strpos($this->urlBuilder->getCurrentUrl(), $paramsStr)) {
                    $newUrl = str_replace($paramsStr, $sortedParamsStr, $this->urlBuilder->getCurrentUrl());
                } else {
                    $newUrl = preg_replace(
                        '/([^:])\\/\\//',
                        '$1/' ,
                        $this->urlBuilder->getBaseUrl() . $request->getPathInfo()
                            . '?' . $sortedParamsStr
                            . '&' . $this->createUrlParams($anotherParam)
                    );
                }
                $this->makeRedirect($newUrl);
                return;
            }
        }
    }

    /**
     * Create url string from array
     * @param array $params
     *
     * @return string
     */
    protected function createUrlParams($params)
    {
        $parts = [];
        foreach ($params as $code => $values) {
            if (is_array($values)) {
                $parts[] = $code . '=' . urlencode(implode(',', $values));
            } else {
                $parts[] = $code . '=' . urlencode($values);
            }
        }
        return implode('&', $parts);
    }

    /**
     * @param string $url
     */
    protected function makeRedirect($url)
    {
        $this->response->setRedirect($url, 301)->sendResponse()->setDispatched(true);
        $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
    }

    /**
     * Can rewrite
     * @param  string $code
     * @return boolean
     */
    protected function _canRewrite($code)
    {
        $enabledAttributeCodes = $this->_dataHelper->getSelectedAttributeCodes();
        $enabledCustomOptions = $this->_getConvertedCustomOptions();
        $additionalFilters = $this->_additionalFilters->toArray();
        $additionalFilters[$this->getCategorVar()] = 'Category';

        return
            in_array($code, $enabledAttributeCodes)
            || in_array($code, $this->_toolbarVars)
            || in_array($code, $enabledCustomOptions)
            || isset($additionalFilters[$code]);
    }

    /**
     * Retrieve attribute vvalue id
     * This method return id of option by option label
     * @param  string $attr
     * @param  string $value
     * @return string
     */
    protected function _getAttributeValueId($attr, $value)
    {
        $value = htmlspecialchars_decode($value);

        if ($this->_filterableAttributes == null) {
            if (false && $fromCache = $this->_cache->load(self::ATTRIBUTES_CACHE_INDEFINER)) {
                $this->_filterableAttributes = $this->_jsonHelper->jsonDecode($fromCache);
            } else {
                $this->_prepareAtrributeValueId();
            }
        }

        if (!isset($this->_filterableAttributes[$attr]) || !isset($this->_filterableAttributes[$attr][$value])) {
            return null;
        }


        return $this->_filterableAttributes[$attr][$value];
    }

    /**
     * Retrieve converted custom options
     * @return array
     */
    protected function _getConvertedCustomOptions()
    {
        $selected = $this->_dataHelper->getSelectedCustomOptions();
        $options = [];
        foreach ($selected as $sel) {
            $options[] = $this->_dataHelper->convertCustomOptionTitle($sel);
        }

        return $options;
    }


    /**
     * Prepare attributes for select
     * Also this data writed to cache
     * @return array
     * [
     *     'attribute_code': [
     *         'option_label': 'option_id',
     *         'option_label': 'option_id',
     *         ...
     *     ],
     *     ....
     * ]
     */
    protected function _prepareAtrributeValueId()
    {
        $attributes = $this->_filterList->getFilters();
        $this->_filterableAttributes = [];

        foreach ($attributes as $attributeCode => $attribute) {

            $this->_filterableAttributes[$attributeCode] = [];

            if ($attribute->getOptions()) {
                foreach ($attribute->getOptions() as $option) {
                    $optionLabel = $this->_dataHelper->getConvertedAttributeValue($option->getLabel());
                    $this->_filterableAttributes[$attributeCode][$optionLabel] = $option->getValue();
                }
            } else {
                // var_dump($attribute);
            }
        }

        $categories = $this->_category->getCollection()
            ->addAttributeToSelect('url_key')
            ->addFieldToFilter('is_active', 1);

        foreach ($categories as $category) {
            $urlKey = $this->_dataHelper->getConvertedAttributeValue($category->getUrlKey());
            $this->_filterableAttributes[$this->getCategorVar()][$urlKey] = $category->getId();
        }

        $this->_cache->save(
            $this->_jsonHelper->jsonEncode($this->_filterableAttributes),
            self::ATTRIBUTES_CACHE_INDEFINER,
            [], // Tags
            3600 //Cache life time
        );

        return $this->_filterableAttributes;
    }

    /**
     * Retrieve category var
     * Or maybe for pretty cat =^.^=
     * @return string pretty
     */
    private function getCategorVar()
    {
        return 'cat';
    }
}
