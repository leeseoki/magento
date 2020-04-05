<?php
/**
 * Webkul MobOrderResource Model
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */


namespace Webkul\Odoomagentoconnect\Model;

use Webkul\Odoomagentoconnect\Api\MobOrderResourceInterface;

/**
 * Defines the implementation class of the calculator service contract.
 */
class MobOrderResource implements MobOrderResourceInterface
{
    
    /**
     * @var MobFactory
     */
    protected $mobOrderResourceFactory;

    /**
     * Constructor.
     *
     * @param MobFactory
     */
    public function __construct(
        MobOrderResourceFactory $mobOrderResourceFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Sales\Model\Service\InvoiceService $invoiceRepository,
        \Magento\Framework\DB\Transaction $invoiceTransaction,
        \Magento\Sales\Model\Convert\Order $shipmentRepository
    ) {
    
        $this->mobOrderResourceFactory = $mobOrderResourceFactory;
        $this->_objectManager = $objectManager;
        $this->_orderInterface = $orderInterface;
        $this->_invoiceRepository = $invoiceRepository;
        $this->_invoiceTransaction = $invoiceTransaction;
        $this->_shipmentRepository = $shipmentRepository;
    }

    /**
     * Create product
     *
     * @param string $orderId
     * @param mixed $itemData
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function orderInvoice($incrementId, $itemData = [])
    {
        $order = $this->_orderInterface->loadByIncrementId($incrementId);
        if ($order->canInvoice()) {
            $invoice = $this->_invoiceRepository->prepareInvoice($order);
            if($invoice->canCapture())
                $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->register();
            $invoice->save();
            $invoiceTransactionSave = $this->_invoiceTransaction->addObject(
                $invoice
            )->addObject(
                $invoice->getOrder()
            );
            $invoiceTransactionSave->save();
            if ($order->hasShipments() == 0) {
                $order->setStatus('processing');
            }
            $order->addStatusHistoryComment(
                __('Invoice #%1 Created from Odoo.', $invoice->getIncrementId())
            )
            ->setIsCustomerNotified(false)
            ->save();
        }
        return true;
    }

    /**
     * Create product
     *
     * @param string $orderId
     * @param mixed $itemData
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function orderShipment($incrementId, $itemData = [])
    {
        $order = $this->_orderInterface->loadByIncrementId($incrementId);
        $shipmentId = 0;
        if ($order->canShip()) {
            $shipment = $this->_shipmentRepository->toShipment($order);
            foreach ($order->getAllItems() as $orderItem) {
                if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                    continue;
                }
                $qtyShipped = 0;
                if ($itemData){
                    $sku = $orderItem->getSku();
                    if(isset($itemData[$sku])){
                        $qtyShipped = $itemData[$sku];
                    }
                } else {
                    $qtyShipped = $orderItem->getQtyToShip();
                }
                if ($qtyShipped) {
                    $shipmentItem = $this->_shipmentRepository->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                    $shipment->addItem($shipmentItem);
                }
            }

            $shipment->register();
            $shipment->getOrder()->setIsInProcess(true);
            try {
                $shipment->save();
                $shipment->getOrder()->save();
                $shipment->save();
                $shipmentId = $shipment->getId();
                if ($order->hasInvoices() == 0) {
                    $order->setStatus('processing');
                }
                $order->addStatusHistoryComment(
                    __('Shipment #%1 Created from Odoo.', $shipment->getIncrementId())
                )
                ->setIsCustomerNotified(false)
                ->save();
            } catch (\Exception $e) {
                return false;
            }
        }
        return $shipmentId;
    }

    /**
     * Create product
     *
     * @param string $orderId
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function orderCancel($incrementId)
    {
        $order = $this->_orderInterface->loadByIncrementId($incrementId);
        try {
            $helper = $this->_objectManager->create('\Webkul\Odoomagentoconnect\Helper\Connection');
            $helper->getSession()->setOperationFrom('odoo');
            $order->cancel()->save();
        }
        catch (\Exception $e) {
            return false;
        }
        $order->addStatusHistoryComment(
            __('Order Cancelled from Odoo.')
        )
        ->setIsCustomerNotified(false)
        ->save();
        return true;
    }
}
