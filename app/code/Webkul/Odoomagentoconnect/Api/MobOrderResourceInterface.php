<?php

namespace Webkul\Odoomagentoconnect\Api;

/**
 * @api
 */
interface MobOrderResourceInterface
{
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
    public function orderInvoice($orderId, $itemData = []);

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
    public function orderShipment($orderId, $itemData = []);

    /**
     * Create product
     *
     * @param string $orderId
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function orderCancel($orderId);
}
