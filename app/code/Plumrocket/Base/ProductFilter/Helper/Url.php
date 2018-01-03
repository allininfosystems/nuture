<?php

namespace Plumrocket\ProductFilter\Helper;

use \Magento\Catalog\Model\Product\ProductList\Toolbar;

class Url extends \Magento\Framework\App\Helper\AbstractHelper
{

    //Default filter separator
    const FILTER_PARAM_SEPARATOR = '-';

    const USE_SEO_URL_CONFIG_PATH = 'general/seo_url';

    /**
     * Toolbar variables
     * @var Array
     */
    protected $_toolbarVars = [
        Toolbar::PAGE_PARM_NAME,
        Toolbar::ORDER_PARAM_NAME,
        Toolbar::DIRECTION_PARAM_NAME,
        Toolbar::MODE_PARAM_NAME,
        Toolbar::LIMIT_PARAM_NAME
    ];

    /**
     * Data helper
     * @var \Plumrocket\ProductFilter\Helper\Data
     */
    protected $_dataHelper;

    /**
     * @param Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Plumrocket\ProductFilter\Helper\Data $dataHelper
    ) {
        $this->_dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * Use seo friendly url
     * @return boolean
     */
    public function useSeoFriendlyUrl()
    {
        return (bool)$this->_dataHelper->getUseSeoFriendlyUrl();
    }

    /**
     * Retrieve url for current item
     * @param  string $code
     * @param  stinrg $value
     * @param  boolean $removeCurrent Removing current value with this parameter
     * @return string
     */
    public function getUrlForItem($code, $value, $removeCurrent = false)
    {
        $value = urlencode($value);
        $currentUrl = $this->_urlBuilder->getCurrentUrl();

        if ($removeCurrent) {
            $currentUrl = preg_replace("/(\/".$code."-[[:alnum:]]+)(\/|\.|\?|$|#)/i", '$2', $currentUrl);
        }

        $delimiters = [];
        if ($this->getCategoryUrlSufix()) {
            $delimiters[] = $this->getCategoryUrlSufix();
        }
        $delimiters[] = '?';

        foreach ($delimiters as $delimiter) {
            $_parsed = explode($delimiter, $currentUrl);

            if (count($_parsed) < 2) {
                continue;
            }

            $currentPath = trim($_parsed[0], '/');

            $url = $currentPath . '/' . $code . $this->getFilterParamSeparator() . $value;
            $url .= $delimiter . $_parsed[1];
            return $url;
        }

        $currentPath = trim($currentUrl, '/');
        $url = $currentPath . '/' . $code . $this->getFilterParamSeparator() . $value;
        return $url;
    }

    /**
     * Retrieve reset url
     * @param  string $code
     * @param  strin $value
     * @return string
     */
    public function getResetUrl($code, $value)
    {
        $value = urlencode(html_entity_decode($value));
        $currentUrl = $this->_urlBuilder->getCurrentUrl();
        $urlParam = DIRECTORY_SEPARATOR . $code . $this->getFilterParamSeparator() . $value;
        $url = str_replace($urlParam, '', $currentUrl);
        return $url;
    }

    /**
     * Retrieve clear all url
     * @param  \Magento\Catalog\Model\Layer $layer
     * @return string
     */
    public function getClearAllUrl($layer)
    {
        $url = $this->_urlBuilder->getCurrentUrl();
        foreach ($layer->getState()->getFilters() as $item) {
            $code = $item->getFilter()->getRequestVar();
            $url = preg_replace("/(".$code."-[[:alnum:]]+)(?:\/|\?|$|#)/i", '', $url);
        }

        return $url;
    }

    /**
     * Retrieve filter param separator
     * @return string
     */
    public function getFilterParamSeparator()
    {
        //This can be rewrited or added new functionality
        return self::FILTER_PARAM_SEPARATOR;
    }

    /**
     * Rertrieve canonical url
     * @return string
     */
    public function getCanonicalUrl()
    {
        $currentUrl = $this->_urlBuilder->getCurrentUrl();
        $parts = explode('?', $currentUrl);
        return $parts[0];
    }

    /**
     * Retrieve toolbar vars
     * @return array
     */
    public function getToolbarVars()
    {
        return $this->_toolbarVars;
    }

    /**
     * Retrieve filter value
     * @return string
     */
    public function getValueByFilter($filter)
    {
        if ($this->useSeoFriendlyUrl()) {

            if ($filter->getFilter()->getRequestVar() != 'price' && $filter->getFilter()->getRequestVar() != 'cat') {
                $value = $this->_dataHelper->getConvertedAttributeValue($filter->getLabel());
            } else {
                $value = $this->_dataHelper->getConvertedAttributeValue($filter->getValue());
            }
        } else {
            $value = $filter->getValue();
        }

        if (is_array($value)) {
            $value = implode('-', $value);
        }

        return $value;
    }

    /**
     * Retrieve category url sufix
     * @return string
     */
    public function getCategoryUrlSufix()
    {
        return (string)$this->_dataHelper->getConfig(\Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator::XML_PATH_CATEGORY_URL_SUFFIX);
    }

    /**
     * Check url
     * If enabled seo friendly urls, then add sufix to the end of url
     * @param  string $url
     * @return string
     */
    public function checkUrl($url)
    {
        if ($this->useSeoFriendlyUrl()) {

            $sufix = $this->getCategoryUrlSufix();

            $currentUrl = $this->_urlBuilder->getUrl('*/*/*', ['_current' => true, '_use_rewrite' => true]);
            $_url = explode(str_replace($sufix, '', $currentUrl), $url);
            if (!empty($_url[1])) {
                $_urlParts = explode('/', str_replace($sufix, '', $_url[1]));
                sort($_urlParts);
                $url = $currentUrl . implode('/', $_urlParts);
            } else {
                $_url = explode('/result/', $url);
                if (!empty($_url[1])) {
                    $_url2 = explode($sufix ? $sufix : '?', $_url[1]);
                    $_urlParts = explode('/', $_url2[0]);
                    sort($_urlParts);
                    $url = str_replace($_url2[0], implode('/', $_urlParts), $url);
                }
            }

            if ($sufix && strpos($url, $sufix) !== false) {
                $url = str_replace($sufix, '', $url);
                $p = strpos($url, '?');
                if ($p !== false) {
                    $url = substr($url, 0, $p) . $sufix . substr($url, $p);
                } else {
                    $url .= $sufix;
                }
            }


        }

        return $url;
    }

}
