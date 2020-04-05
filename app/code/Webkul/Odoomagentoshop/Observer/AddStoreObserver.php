<?php
/**
 * Webkul Odoomagentoshop AddStoreObserver Observer Model
 *
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoshop\Observer;

use Magento\Framework\Event\Manager;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Webkul\Odoomagentoconnect\Helper\Connection;
use xmlrpc_client;
use xmlrpcval;
use xmlrpcmsg;

/**
 * Webkul Odoomagentoshop Store Add Observer Class
 */
class AddStoreObserver implements ObserverInterface
{

    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Session $session,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_objectManager = $objectManager;
        $this->_session = $session;
        $this->_logger = $logger;
    }

    /**
     * Category save after event handler
     *
     * @param  \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $extraFieldArray = $this->_session->getExtraFieldArray();
        $mageOrderId = $observer->getEvent()->getMageOrderId();
        $thisOrder = $this->_objectManager->create(\Magento\Sales\Model\Order::class)
            ->load($mageOrderId);
        $storeId = $thisOrder->getStoreId();
        $mappingcollection = $this->_objectManager
            ->create(\Webkul\Odoomagentoshop\Model\Shop::class)
            ->getCollection()
            ->addFieldToFilter('magento_id', ['eq'=>$storeId]);
        foreach ($mappingcollection as $col) {
            $odooId = $col->getOdooId();
            $extraFieldArray['shop_id'] = new xmlrpcval($odooId, "int");
        }
        $this->_session->setExtraFieldArray($extraFieldArray);
    }
}
