<?php

/**
 * CoderKube
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category  CoderKube
 * @package   Coderkube_Deleteorder
 * @copyright Copyright (c) CoderKube (http://coderkube.com/)
 */

namespace Coderkube\Deleteorder\Controller\Adminhtml\Deleteorder;

abstract class AbstractDeleteorder extends \Magento\Backend\App\Action
{
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('deleteorder');
    }
}
