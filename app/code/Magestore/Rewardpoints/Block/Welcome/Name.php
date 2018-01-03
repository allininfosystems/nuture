<?php
namespace Magestore\Rewardpoints\Block\Welcome;

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * RewardPoints Name and Image Block
 *
 * @category    Magestore
 * @package     Magestore_RewardPoints
 * @author      Magestore Developer
 */
class Name extends \Magestore\Rewardpoints\Block\Name {
    public function _toHtml() {
        parent::_toHtml();
        return $this->_objectManager->create('Magestore\Rewardpoints\Helper\Point')->getPluralName();
    }

}
