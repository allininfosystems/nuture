<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Shopbybrand
 * @copyright   Copyright (c) 2017 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Shopbybrand\Controller;

/**
 * Class Router
 *
 * @package Mageplaza\Shopbybrand\Controller
 */
class Router implements \Magento\Framework\App\RouterInterface
{
    /** @var \Magento\Framework\App\ActionFactory */
    protected $actionFactory;

    /** @var \Mageplaza\Shopbybrand\Helper\Data */
    protected $_helper;

    /**
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Mageplaza\Shopbybrand\Helper\Data   $helper
     */
    public function __construct(\Magento\Framework\App\ActionFactory $actionFactory,
        \Mageplaza\Shopbybrand\Helper\Data $helper
    ) {
        $this->actionFactory = $actionFactory;
        $this->_helper = $helper;
    }

    /**
     * Validate and Match Brand Page and modify request
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return \Magento\Framework\App\ActionInterface|null
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $urlSuffix = $this->_helper->getUrlSuffix();

        if ($urlSuffix) {
            $pos = strpos($identifier, $urlSuffix);
            if ($pos) {
                $identifier = substr($identifier, 0, $pos);
            }
        }

        $routePath = explode('/', $identifier);
        $routeSize = sizeof($routePath);
        if (!$this->_helper->isBrandRoute($routePath, $routeSize)) {
            return null;
        }

        $request->setModuleName('mpbrand')->setAlias(
                \Magento\Framework\Url::REWRITE_REQUEST_PATH_ALIAS,
                trim($request->getPathInfo(), '/')
            );

        if ($routeSize == 1) {
            $request->setControllerName('index')->setActionName('index')
                ->setPathInfo('/mpbrand/index/index');
        } else {
            switch ($routePath[1]) {
                case \Mageplaza\Shopbybrand\Helper\Data::CATEGORY:
                    $catId = $this->_helper->getCategoryByUrlKey($routePath[2]);
                    $request->setControllerName('category')->setActionName(
                            'view'
                        )->setParam('cat_id', $catId)->setPathInfo(
                            '/mpbrand/category/view'
                        );
                    break;

                default:
                    $request->setControllerName('index')->setActionName('view')
                        ->setParam('brand_key', $routePath[1])->setPathInfo(
                            '/mpbrand/index/view'
                        );
            }

        }

        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward'
        );
    }

}
