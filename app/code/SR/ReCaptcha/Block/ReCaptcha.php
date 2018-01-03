<?php
namespace SR\ReCaptcha\Block;

class ReCaptcha extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \SR\ReCaptcha\Helper\Data $dataHelper
     */
    protected $dataHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \SR\ReCaptcha\Helper\Data $dataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \SR\ReCaptcha\Helper\Data $dataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
    }

    public function isEnabled()
    {
        return $this->dataHelper->isEnabled();
    }

    public function getSiteKey()
    {
        return $this->dataHelper->getSiteKey();
    }
}