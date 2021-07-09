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
 * @copyright Copyright (c) CoderKube(http://coderkube.com/)
 */

namespace Coderkube\Deleteorder\Model;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo\Collection as CreditmemoCollection;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection;
use Magento\Sales\Model\ResourceModel\Order\Payment\Transaction\Collection as TransactionCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection as ShipmentCollection;

class Order extends AbstractModel
{
    /**
     * @var ProductMetadataInterface
     */
    public $appProductMetadataInterface;
    /**
     * @var Resource
     */
    public $modelResource;
    /**
     * @var ManagerInterface
     */
    public $messageManagerInterface;
    /**
     * @var Collection
     */
    public $invoiceCollection;
    /**
     * @var ShipmentCollection
     */
    public $shipmentCollection;
    /**
     * @var CreditmemoCollection
     */
    public $creditmemoCollection;
    /**
     * @var TransactionCollection
     */
    public $transactionCollection;
    public function __construct(
        Context $context,
        Registry $registry,
        ProductMetadataInterface $appProductMetadataInterface,
        \Magento\Framework\App\ResourceConnection $modelResource,
        ManagerInterface $messageManagerInterface,
        Collection $invoiceCollection,
        ShipmentCollection $shipmentCollection,
        CreditmemoCollection $creditmemoCollection,
        TransactionCollection $transactionCollection,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->appProductMetadataInterface = $appProductMetadataInterface;
        $this->modelResource = $modelResource;
        $this->messageManagerInterface = $messageManagerInterface;
        $this->invoiceCollection = $invoiceCollection;
        $this->shipmentCollection = $shipmentCollection;
        $this->creditmemoCollection = $creditmemoCollection;
        $this->transactionCollection = $transactionCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    public function deleteOrder($orderIds = [])
    {
        $this->deteleRelated($orderIds);
        if ($this->_delete($orderIds)) {
            return true;
        }
        return false;
    }
    public function _delete($orderIds = [])
    {
        $orderIds = '(' . implode(",", $orderIds) . ')';
        $resource = $this->modelResource;
        $write = $resource->getConnection('core_write');
        $connection = $resource->getConnection();
        $saleFlatOrder  = $resource->getTableName('sales_order');
        $saleFlatOrderGrid = $resource->getTableName('sales_order_grid');
        try {
            $connection->delete(
                $saleFlatOrder,
                ['entity_id IN ' . $orderIds]
            );
            $connection->delete(
                $saleFlatOrderGrid,
                ['entity_id IN ' . $orderIds]
            );
        } catch (\Exception $e) {
            $this->messageManagerInterface->addError($e->getMessage());
        }
        return true;
    }
    public function deteleRelated($orderIds = [])
    {
        try {
            // Remove Invoice Entry From Invoice Tabel
            $invoices = $this->invoiceCollection->addFieldToFilter('order_id', ['in', $orderIds]);
            foreach ($invoices as $invoice) {
                $this->deleteItem($invoice);
            }
            // Remove Invoice Entry From Invoice Grid
            $this->deleteGridData($orderIds, 'sales_invoice_grid');
            // Remove Shipment Entry From Shipment Tabel
            $shipments = $this->shipmentCollection->addFieldToFilter('order_id', ['in', $orderIds]);
            foreach ($shipments as $shipment) {
                $this->deleteItem($shipment);
            }
            // Remove Shipment Entry From Shipment Grid
            $this->deleteGridData($orderIds, 'sales_shipment_grid');
            // Remove Creditmemo Entry From Creditmemo Tabel
            $creditmemos = $this->creditmemoCollection->addFieldToFilter('order_id', ['in', $orderIds]);
            foreach ($creditmemos as $creditmemo) {
                $this->deleteItem($creditmemo);
            }
            // Remove Creditmemo Entry From Creditmemo Grid
            $this->deleteGridData($orderIds, 'sales_creditmemo_grid');
            $transactions = $this->transactionCollection->addFieldToFilter('order_id', ['in', $orderIds]);
            foreach ($transactions as $transaction) {
                $this->deleteItem($transaction);
            }
        } catch (\Exception $e) {
            $this->messageManagerInterface->addError($e->getMessage());
        }
    }
    public function deleteGridData($orderId, $Tabelname)
    {
        $orderId              = '(' . implode(",", $orderId) . ')';
        $resource             = $this->modelResource;
        $write                = $resource->getConnection('core_write');
        $connection = $resource->getConnection();
        $salesGrid            = $resource->getTableName($Tabelname);
        try {
            $connection->delete(
                $salesGrid,
                ['order_id IN ' . $orderId]
            );
        } catch (\Exception $e) {
            $this->messageManagerInterface->addError($e->getMessage());
        }
        return true;
    }
    private function deleteItem($item)
    {
        $item->delete();
    }
}
