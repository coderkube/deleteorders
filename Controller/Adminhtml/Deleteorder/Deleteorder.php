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

use Coderkube\Deleteorder\Model\OrderFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\ObjectManager;

class Deleteorder extends AbstractDeleteorder
{
    /**
     * @var OrderFactory
     */
    public $modelOrderFactory;
    public function __construct(
        Context $context,
        OrderFactory $modelOrderFactory
    ) {
        $this->modelOrderFactory = $modelOrderFactory;
        parent::__construct($context);
    }
    public function execute()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        if (empty($orderId)) {
            $this->messageManager->addError(__('There is no order to process'));
            $this->_redirect('sales/order/index');
            return;
        }
        try {
            if ($this->modelOrderFactory->create()->deleteOrder([$orderId])) {
                $this->messageManager->addSuccess(__('Order successfully deleted'));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError(__($e->getMessage()));
        }
        $this->_redirect('sales/order/index');
    }
}
