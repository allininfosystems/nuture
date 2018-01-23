<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ZohocrmIntegration extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ZohocrmIntegration
 * @author   ThaoPV
 */
namespace Magenest\ZohocrmIntegration\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field as FormField;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class GetAuth
 *
 * @package Magenest\ZohocrmIntegration\Block\Adminhtml\System\Config
 */
class GetAuth extends FormField
{
    /**
     * Email
     *
     * @var string
     */
    protected $_email = 'zohocrm_config_email';

    /**
     * Password
     *
     * @var string
     */
    protected $_password = 'zohocrm_config_passwd';

    /**
     * Get Auth Token Label
     *
     * @var string
     */
    protected $_authButtonLabel = 'Get Auth Token';

    /**
     * Set Email ZohoCRM
     *
     * @param  $email
     * @return $this
     */
    public function setEmail($email)
    {
        $this->_email = $email;
        return $this;
    }

    /**
     * Get Email ZohoCRM
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->_email;
    }

    /**
     * Set password
     *
     * @param  $password
     * @return $this
     */
    public function setPasswd($password)
    {
        $this->_password = $password;
        return $this;
    }

    /**
     * Get Password
     *
     * @return string
     */
    public function getPasswd()
    {
        return $this->_password;
    }

    /**
     * Set Get Auth Token Button label
     *
     * @param  $getAuthButtonLabel
     * @return $this
     */
    public function setButtonLabel($getAuthButtonLabel)
    {
        $this->_authButtonLabel = $getAuthButtonLabel;
        return $this;
    }

    /**
     * Set template to itself
     *
     * @return \Magenest\ZohocrmIntegration\Block\Adminhtml\System\Config\GetAuth
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->getTemplate()) {
            $this->setTemplate('system/config/getauth.phtml');
        }

        return $this;
    }

    /**
     * Unset some non-related element parameters
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element)
    {
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Get the button and scripts contents
     *
     * @param  \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $originalData = $element->getOriginalData();
        $buttonLabel  = !empty($originalData['button_label']) ? $originalData['button_label'] : $this->_authButtonLabel;
        $this->addData(
            [
             'button_label' => __($buttonLabel),
             'html_id'      => $element->getHtmlId(),
             'ajax_url'     => $this->_urlBuilder->getUrl('zohocrm/system_config_getauth/getAuth'),
            ]
        );

        return $this->_toHtml();
    }
}
