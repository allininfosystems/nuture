<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */
namespace Magestore\Sociallogin\Block;

class Autosociallogin extends Sociallogin
{
    protected $_position = null;

    public function getShownPositions()
    {
        $shownpositions = $this->_dataHelper->getConfig(\Magestore\Sociallogin\Helper\Data::XML_PATH_POSITION, $this->_storeManager->getStore()->getId());
        $shownpositions = explode(',', $shownpositions);
        return $shownpositions;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getPosition()
    {
        return $this->position;
    }

    protected function _beforeToHtml()
    {

        if (!$this->getPosition() || !in_array($this->getPosition(), $this->getShownPositions())) {

            $this->setTemplate(null);
        }

        return parent::_beforeToHtml();
    }
}