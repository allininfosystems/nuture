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
 * @package   mirasvit/module-core
 * @version   1.2.21
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Core\Model;

use Magento\Framework\Module\FullModuleList;
use Magento\Framework\Module\Dir\Reader as DirReader;

class Module
{
    private static $modules = null;

    protected $fullModuleList;

    protected $dirReader;

    protected $name;

    protected $moduleName;

    protected $installedVersion;

    protected $latestVersion;

    protected $url;

    public function __construct(
        FullModuleList $fullModuleList,
        DirReader $dirReader
    ) {
        $this->fullModuleList = $fullModuleList;
        $this->dirReader = $dirReader;
    }

    public function getAllModules()
    {
        if (self::$modules == null) {
            self::$modules = json_decode(file_get_contents('http://mirasvit.com/pc/modules/'), true);
        }

        return self::$modules;
    }

    public function getInstalledModules()
    {
        $modules = [];
        foreach ($this->fullModuleList->getAll() as $module) {
            if (substr($module['name'], 0, strlen('Mirasvit_')) == 'Mirasvit_') {
                $modules[] = $module['name'];
            }
        }

        return $modules;
    }

    public function load($moduleName)
    {
        $modules = $this->getAllModules();

        if (key_exists(strtolower($moduleName), $modules)) {
            $m = $modules[strtolower(strtolower($moduleName))];

            $this->moduleName = $moduleName;
            $this->name = $m['name'];
            $this->latestVersion = $m['version'];
            $this->url = $m['url'];

            $composer = $this->getComposerInformation($moduleName);

            if ($composer) {
                $this->installedVersion = $composer['version'];
            }
        }

        return $this;
    }

    public function getComposerInformation($moduleName)
    {
        $dir = $this->dirReader->getModuleDir("", $moduleName);

        if (file_exists($dir.'/composer.json')) {
            return json_decode(file_get_contents($dir.'/composer.json'), true);
        }

        if (file_exists($dir.'/../../composer.json')) {
            return json_decode(file_get_contents($dir.'/../../composer.json'), true);
        }

        return false;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function getInstalledVersion()
    {
        return $this->installedVersion;
    }

    public function getLatestVersion()
    {
        return $this->latestVersion;
    }

    public function getUrl()
    {
        return $this->url;
    }
}