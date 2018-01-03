<?php

namespace Magestore\Rewardpoints\Helper;

class Config extends \Magento\Framework\App\Helper\AbstractHelper {
    /**
     * @param $key
     * @param null $store
     * @return mixed
     */
    public function getConfig($key, $store = null) {
        return $this->scopeConfig->getValue(
            $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
    }

    /**
     * @param $field
     * @param null $store
     * @return mixed
     */
    public function getGeneralConfig($field , $store = null){
        return $this->getConfig('rewardpoints/general/'.$field,$store);
    }

    /**
     * @param $field
     * @param null $store
     * @return mixed
     */
    public function getEarningConfig($field , $store = null){
        return $this->getConfig('rewardpoints/earning/'.$field,$store);
    }

    /**
     * @param $field
     * @param null $store
     * @return mixed
     */
    public function getSpendingConfig($field , $store = null){
        return $this->getConfig('rewardpoints/spending/'.$field,$store);
    }

    /**
     * @param $field
     * @param null $store
     * @return mixed
     */
    public function getDisplayConfig($field , $store = null){
        return $this->getConfig('rewardpoints/display/'.$field,$store);
    }

    /**
     * @param $field
     * @param null $store
     * @return mixed
     */
    public function getEmailConfig($field , $store = null){
        return $this->getConfig('rewardpoints/email/'.$field,$store);
    }
}
