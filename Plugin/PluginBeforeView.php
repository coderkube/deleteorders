<?php

/**
 * CoderKube
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    CoderKube
 * @package     Coderkube_Deleteorder
 * @copyright Copyright (c) CoderKube(http://coderkube.com/)
 */

namespace Coderkube\Deleteorder\Plugin;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\UrlInterface;
use Coderkube\Deleteorder\Helper\Data as HelperData;
use \Magento\Sales\Block\Adminhtml\Order\View;

class PluginBeforeView
{
    public function __construct(
        UrlInterface $urlBuilder,
        HelperData $helperData
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->helperData = $helperData;
    }
    public function beforeGetOrderId(View $subject)
    {
        if (!$this->helperData->isEnabled()) {
            return;
        }
        $message = __('Do you want to delete this order?');
        $subject->addButton(
            'delete_btn',
            [
                'label'     => __($this->helperData->getOrderButtonLabel()),
                'class'     =>  'go',
                'onclick'   =>  "confirmSetLocation('" . $message . "', '" . $this->getDeleteOrderUrl() . "')"
            ]
        );
        return null;
    }
    public function getDeleteOrderUrl()
    {
        return $this->urlBuilder->getUrl('deleteorder/deleteorder/deleteorder', ['_current' => true]);
    }
}
