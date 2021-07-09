<?php

/**
 * CoderKube
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  Coderkube
 * @package   Coderkube_Deleteorder
 * @copyright Copyright (c) CoderKube(http://coderkube.com/)
 */

namespace Coderkube\Deleteorder\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    public function isEnabled()
    {
        return $this->scopeConfig->getValue('deleteorder/general/enable', ScopeInterface::SCOPE_STORE);
    }
    public function getOrderButtonLabel()
    {
        return $this->scopeConfig->getValue('deleteorder/general/btnheading', ScopeInterface::SCOPE_STORE);
    }
}
