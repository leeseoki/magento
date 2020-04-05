<?php
/**
 * Webkul Odoomagentoconnect Order ResourceModel
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomagentoconnect\Model\ResourceModel;

use Webkul\Odoomagentoconnect\Helper\Connection;
use xmlrpc_client;
use xmlrpcval;
use xmlrpcmsg;

class Order extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Session $session,
        \Webkul\Odoomagentoconnect\Model\Customer $customerModel,
        Connection $connection,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Customer $customerMapping,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Currency $currencyModel,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Carrier $carrierMapping,
        \Magento\Catalog\Model\Product $catalogModel,
        \Magento\Sales\Model\Order\Item $orderItemModel,
        \Webkul\Odoomagentoconnect\Model\Product $productModel,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Product $productMapping,
        \Magento\Tax\Model\Calculation\Rate $taxRateModel,
        \Webkul\Odoomagentoconnect\Model\Tax $taxModel,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Tax $taxMapping,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configModel,
        \Magento\Sales\Model\ResourceModel\Order\Tax\Item $taxItemModel,
        \Webkul\Odoomagentoconnect\Model\Payment $paymentModel,
        \Webkul\Odoomagentoconnect\Model\ResourceModel\Payment $paymentMapping,
        $resourcePrefix = null
    ) {
        $this->_connection = $connection;
        $this->_catalogModel = $catalogModel;
        $this->_currencyModel = $currencyModel;
        $this->_customerModel = $customerModel;
        $this->_customerMapping = $customerMapping;
        $this->_carrierMapping = $carrierMapping;
        $this->_scopeConfig = $scopeConfig;
        $this->_eventManager = $eventManager;
        $this->_orderItemModel = $orderItemModel;
        $this->_productModel = $productModel;
        $this->_productMapping = $productMapping;
        $this->_taxRateModel = $taxRateModel;
        $this->_taxModel = $taxModel;
        $this->_taxMapping = $taxMapping;
        $this->_taxItemModel = $taxItemModel;
        $this->_configModel = $configModel;
        $this->_paymentModel = $paymentModel;
        $this->_paymentMapping = $paymentMapping;
        $this->_session = $session;
        $this->_objectManager = $objectManager;
        parent::__construct($context, $resourcePrefix);
    }

    public function exportOrder($thisOrder, $quote=false)
    {
        $odooId = 0;
        $mageOrderId = $thisOrder->getId();
        $currencyCode = $thisOrder->getOrderCurrencyCode();
        $pricelistId = $this->_currencyModel
                            ->syncCurrency($currencyCode);
        if (!$pricelistId) {
            return [0,0, "Odoo pricelist id not found"];
        }
        $incrementId = $thisOrder->getIncrementId();
        $erpAddressArray = $this->getErpOrderAddresses($thisOrder);

        if (count(array_filter($erpAddressArray)) == 3) {
            $lineids = '';
            $partnerId = $erpAddressArray[0];
            $odooOrder = $this->createOdooOrder($thisOrder, $pricelistId, $erpAddressArray);
            $odooId = $odooOrder;
            if ($odooId) {
                $lineids = $this->createOdooOrderLine($thisOrder, $odooId, $quote);
                $includesTax = $this->_scopeConfig->getValue('tax/calculation/price_includes_tax');
                /*if ($includesTax) {
                    if ($thisOrder['discount_amount'] != 0) {
                        $voucherLineId = $this->createOdooOrderVoucherLine($thisOrder, $odooId);
                        $lineids .= $voucherLineId;
                    }
                }*/
                $this->_eventManager
                        ->dispatch(
                            'odoo_order_sync_after',
                            ['mage_order_id' => $mageOrderId, 'odoo_order_id' => $odooId]
                        );
                if ($thisOrder->getShippingDescription()) {
                    $shippingLineId = $this->createOdooOrderShippingLine($thisOrder, $odooId);
                    $lineids .= $shippingLineId;
                }
                /* Creating Order Mapping At both End..*/
                $this->createOrderMapping($thisOrder, $odooId, $partnerId, $lineids);

                $draftState = $this->_scopeConfig->getValue('odoomagentoconnect/order_settings/draft_order');
                $autoInvoice = $this->_scopeConfig->getValue('odoomagentoconnect/order_settings/invoice_order');
                $autoShipment = $this->_scopeConfig->getValue('odoomagentoconnect/order_settings/ship_order');
                if (!$draftState) {
                    $this->confirmOdooOrder($odooId);
                }
                if ($thisOrder->hasInvoices() && $autoInvoice==1) {
                    $this->invoiceOdooOrder($thisOrder, $odooId, false, $partnerId);
                }

                if ($thisOrder->hasShipments() && $autoShipment == 1) {
                    $this->deliverOdooOrder($thisOrder, $odooId);
                }
                return $odooId;
            } else {
                return $odooId;
            }
        } else {
            return $odooId;
        }
    }

    public function createOdooOrder($thisOrder, $pricelistId, $erpAddressArray)
    {
        $odooOrder = [];
        $extraFieldArray = [];
        $odooOrderId = 0;
        $partnerId = $erpAddressArray[0];
        $partnerInvoiceId = $erpAddressArray[1];
        $partnerShippingId = $erpAddressArray[2];
        $mageOrderId = $thisOrder->getId();
        $this->_session->setExtraFieldArray($extraFieldArray);
        $this->_eventManager->dispatch('odoo_order_sync_before', ['mage_order_id' => $mageOrderId]);
        $extraFieldArray = $this->_session->getExtraFieldArray();

        $helper = $this->_connection;
        $helper->getSocketConnect();
        $userId = $helper->getSession()->getUserId();
        $extraFieldArray = $this->_session->getExtraFieldArray();
        $incrementId = $thisOrder->getIncrementId();
        $client = $helper->getClientConnect();
        $context = $helper->getOdooContext();
        $warehouseId = $this->_session->getErpWarehouse();
        $orderArray =  [
                    'partner_id'=>new xmlrpcval($partnerId, "int"),
                    'partner_invoice_id'=>new xmlrpcval($partnerInvoiceId, "int"),
                    'partner_shipping_id'=>new xmlrpcval($partnerShippingId, "int"),
                    'pricelist_id'=>new xmlrpcval($pricelistId, "int"),
                    'date_order'=>new xmlrpcval($thisOrder->getCreatedAt(), "string"),
                    'client_order_ref'=>new xmlrpcval($incrementId, "string"),
                    // 'warehouse_id'=>new xmlrpcval($warehouseId, "int"),
                    'channel'=>new xmlrpcval('magento', "string"),
                ];
        $allowSequence = $this->_scopeConfig->getValue('odoomagentoconnect/order_settings/order_name');
        if ($allowSequence) {
            $orderArray['name'] = new xmlrpcval($incrementId, "string");
        }
        /* Adding Shipping Information*/
        if ($thisOrder->getShippingMethod()) {
            $shippingMethod = $thisOrder->getShippingMethod();
            $shippingCode = explode('_', $shippingMethod);
            if ($shippingCode) {
                $shippingCode = $shippingCode[0];
                $erpCarrierId =  $this->_carrierMapping
                                    ->checkSpecificCarrier($shippingCode);
                if ($erpCarrierId > 0) {
                    $orderArray['carrier_id'] = new xmlrpcval($erpCarrierId, "int");
                }
            }
        }
        /* Adding Payment Information*/
        $paymentMethod = $thisOrder->getPayment()->getMethodInstance()->getTitle();
        if ($paymentMethod) {
            $paymentInfo = 'Payment Information:- '.$paymentMethod;
            $mappingcollection = $this->_paymentModel
                ->getCollection()
                ->addFieldToFilter('magento_id', $paymentMethod)
                ->getFirstItem()
                ->getData();
            $orderArray['note'] = new xmlrpcval(urlencode($paymentInfo), "string");
            if ($mappingcollection) {
                $orderArray['payment_method_id'] = new xmlrpcval($mappingcollection['odoo_payment_id'], "string");
                $orderArray['workflow_process_id'] = new xmlrpcval($mappingcollection['odoo_workflow_id'], "string");
            }
        }
        /* Adding Extra Fields*/
        foreach ($extraFieldArray as $field => $value) {
            $orderArray[$field]= $value;
        }
        $msg = new xmlrpcmsg('execute');
        $msg->addParam(new xmlrpcval($helper::$odooDb, "string"));
        $msg->addParam(new xmlrpcval($userId, "int"));
        $msg->addParam(new xmlrpcval($helper::$odooPwd, "string"));
        $msg->addParam(new xmlrpcval("sale.order", "string"));
        $msg->addParam(new xmlrpcval("create", "string"));
        $msg->addParam(new xmlrpcval($orderArray, "struct"));
        $msg->addParam(new xmlrpcval($context, "struct"));
        $resp = $client->send($msg);
        if ($resp->faultcode()) {
            $error = "Export Error, Order ".$incrementId." >>".$resp->faultString();
            $helper->addError($error);
        } else {
            $odooOrderId = $resp->value()->me["int"];
        }
        return $odooOrderId;
    }

    public function createOdooOrderLine($thisOrder, $odooId, $thisQuote=false)
    {
        $erpProductId = 0;
        $lineIds = '';
        $items = $thisOrder->getAllItems();
        if (!$items) {
            return false;
        }
        /* Odoo Conncetion Data*/
        $helper = $this->_connection;
        $userId = $helper->getSession()->getUserId();
        $context = $helper->getOdooContext();
        $client = $helper->getClientConnect();
        
        $mageOrderId = $thisOrder->getId();
        $incrementId = $thisOrder->getIncrementId();
        $resource = $this->_objectManager->create('Magento\Framework\App\ResourceConnection');
        $write = $resource->getConnection('default');
        $shippingIncludesTax = $this->_scopeConfig->getValue('tax/calculation/shipping_includes_tax');
        $priceIncludesTax = $this->_scopeConfig->getValue('tax/calculation/price_includes_tax');

        foreach ($items as $item) {
            $itemId = $item->getId();
            $itemDesc = $item->getName();
            $productId = $item->getProductId();
            $product = $this->_catalogModel->load($productId);
            if ($priceIncludesTax) {
                $basePrice = $item->getPriceInclTax();
            } else {
                $basePrice = $item->getPrice();
            }
            $itemTaxPercent = $item->getTaxPercent();
            $itemType = $item->getProductType();
            if ($itemType == 'configurable') {
                continue;
            }
            if ($itemType == 'bundle') {
                $priceType = $product->getPriceType();
                if (!$priceType) {
                    $basePrice = 0;
                }
            }
            $discountAmount = 0;
            $discountAmount = $item->getDiscountAmount();
            $parent = false;
            if ($item->getParentItemId() != null) {
                $parentId = $item->getParentItemId();
                $parent = $this->_orderItemModel->load($parentId);
                if ($parent->getProductType() == 'configurable') {
                    if ($priceIncludesTax) {
                        $basePrice = $parent->getPriceInclTax();
                    } else {
                        $basePrice = $parent->getPrice();
                    }
                    $itemTaxPercent = $parent->getTaxPercent();

                    $discountAmount = $parent->getDiscountAmount();
                }

                $itemId = $parentId;
            }
            /*
                Fetching Odoo Product Id
            */
            $orderedQty = $item->getQtyOrdered();
            $mappingcollection = $this->_productModel
                                        ->getCollection()
                                        ->addFieldToFilter('magento_id', ['eq'=>$productId]);
            if ($mappingcollection->getSize() > 0) {
                foreach ($mappingcollection as $map) {
                    $erpProductId = $map->getOdooId();
                }
            } else {
                $erpProductId = $this->syncProduct($productId);
            }
            if (!$erpProductId) {
                $error = "Odoo Product Not Found For Order ".$incrementId." Product id = ".$productId;
                $helper->addError($error);
                continue;
            }
            $orderLineArray =  [
                        'order_id'=>new xmlrpcval($odooId, "int"),
                        'product_id'=>new xmlrpcval($erpProductId, "int"),
                        'price_unit'=>new xmlrpcval($basePrice, "string"),
                        'product_uom_qty'=>new xmlrpcval($orderedQty, "string"),
                        'name'=>new xmlrpcval(urlencode($itemDesc), "string")
                    ];
        /**************** checking tax applicable & getting mage tax id per item ************/
            if ($itemTaxPercent > 0) {
                $itemTaxes = [];
                if ($thisQuote) {
                    $qItems = $thisQuote->getAllItems();
                    $oQtItemId = $item->getQuoteItemId();
                    if ($parent) {
                        $oQtItemId = $parent->getQuoteItemId();
                    }
                    foreach ($qItems as $qItem) {
                        $qItemId = $qItem->getItemId();
                        $appliedTaxes = $qItem['applied_taxes'];
                        if ($qItemId == $oQtItemId && $appliedTaxes) {
                            foreach ($appliedTaxes as $appliedTaxe) {
                                $taxCode = $appliedTaxe['id'];
                                $erpTaxId = $this->getOdooTaxId($taxCode);
                                if ($erpTaxId) {
                                    array_push($itemTaxes, new xmlrpcval($erpTaxId, "int"));
                                }
                            }
                            break;
                        }
                    }
                }
                $tableName = $resource->getTableName('sales_order_tax_item');
                $taxItems = $write->query("SELECT * FROM ".$tableName." WHERE item_id='".$itemId."'")->fetchAll();
                if ($taxItems && empty($itemTaxes)) {
                    foreach ($taxItems as $itemTax) {
                        $erpTaxId = 0;
                        $tableName = $resource->getTableName('sales_order_tax');
                        $select = "SELECT code FROM ".$tableName;
                        $queryTax = $select." WHERE tax_id='".$itemTax['tax_id']."' AND order_id= '".$mageOrderId."'";
                        $orderTax = $write->query($queryTax);
                        $taxCodeResult = $orderTax->fetch();
                        
                        $taxCode = $taxCodeResult["code"];
                        $erpTaxId = $this->getOdooTaxId($taxCode);

                        /******************** getting erp tax id ******************/
                        if ($erpTaxId) {
                            array_push($itemTaxes, new xmlrpcval($erpTaxId, "int"));
                        }
                    }
                } else {
                    $tableName = $resource->getTableName('sales_order_tax');
                    $orderTax = $write->query("SELECT code FROM ".$tableName." WHERE order_id= '".$mageOrderId."'");
                    $taxCodeResult = $orderTax->fetch();
                    if ($taxCodeResult) {
                        $taxCode = $taxCodeResult["code"];
                        $erpTaxId = $this->getOdooTaxId($taxCode);
                        if ($erpTaxId) {
                            array_push($itemTaxes, new xmlrpcval($erpTaxId, "int"));
                        }
                    }
                }
                $orderLineArray['tax_id'] = new xmlrpcval($itemTaxes, "array");
            } else {
                $itemTaxes = [];
                $taxRateData = $this->_taxRateModel
                                    ->getCollection()->addFieldToFilter('rate', 0)
                                    ->getData();
                if (count($taxRateData)) {
                    foreach ($taxRateData as $map) {
                        $taxMapData = $this->_taxModel
                                            ->load($map['tax_calculation_rate_id'], "magento_id")
                                            ->getData();
                        if (count($taxMapData)) {
                            $erpTaxId = $taxMapData['odoo_id'];
                            if ($erpTaxId) {
                                array_push($itemTaxes, new xmlrpcval($erpTaxId, "int"));
                            }
                            $orderLineArray['tax_id'] = new xmlrpcval($itemTaxes, "array");
                            break;
                        }
                    }
                }
            }

            $lineCreate = new xmlrpcmsg('execute');
            $lineCreate->addParam(new xmlrpcval($helper::$odooDb, "string"));
            $lineCreate->addParam(new xmlrpcval($userId, "int"));
            $lineCreate->addParam(new xmlrpcval($helper::$odooPwd, "string"));
            $lineCreate->addParam(new xmlrpcval("bridge.backbone", "string"));
            $lineCreate->addParam(new xmlrpcval("create_order_line", "string"));
            $lineCreate->addParam(new xmlrpcval($orderLineArray, "struct"));
            $lineCreate->addParam(new xmlrpcval($context, "struct"));
            $lineResp = $client->send($lineCreate);
            if ($lineResp->faultCode()) {
                $faultString = $lineResp->faultString();
                $error = "Item Sync Error, Order ".$incrementId.", Product id = ".$productId.'Error:-'.$faultString;
                $helper->addError($error);
                continue;
            }
            $lineId = $lineResp->value()->me["int"];
            $lineIds .= $lineId.",";
            if ($discountAmount != 0) {
                $taxes = '';
                if (isset($orderLineArray['tax_id'])) {
                    $taxes = $orderLineArray['tax_id'];
                }
                $productName = $product->getName();
                $voucherLineId = $this->createOdooOrderLineVoucherLine(
                    $thisOrder,
                    $discountAmount,
                    $odooId,
                    $taxes,
                    $productName
                );
                $lineIds .= $voucherLineId.",";
            }
        }
        return $lineIds;
    }
    
    public function syncProduct($productId)
    {
        $odooProductId = 0;
        $response = $this->_productMapping
            ->createSpecificProduct($productId);
        if ($response['odoo_id'] > 0) {
            return $response['odoo_id'];
        }
        return $odooProductId;
    }

    public function getOdooTaxId($taxCode)
    {
        $erpTaxId = 0;
        if ($taxCode) {
            $collection = $this->_taxRateModel
                                ->getCollection()
                                ->addFieldToFilter('code', ['eq'=>$taxCode])
                                ->getAllIds();

            foreach ($collection as $rateId) {
                $mappingcollection = $this->_taxModel
                                            ->getCollection()
                                            ->addFieldToFilter('magento_id', ['eq'=>$rateId]);
                                            
                if (count($mappingcollection)) {
                    foreach ($mappingcollection as $mapping) {
                        $erpTaxId = $mapping->getOdooId();
                    }
                } else {
                    $response = $this->_taxMapping
                                    ->createSpecificTax($rateId);

                    if ($response['odoo_id']) {
                        $erpTaxId = $response['odoo_id'];
                    }
                }
            }
        }
        return $erpTaxId;
    }

    public function getTaxId($mageOrderId)
    {
        $resource = $this->_objectManager->create('Magento\Framework\App\ResourceConnection');
        $write = $resource->getConnection('default');
        $tableName = $resource->getTableName('sales_order_tax');
        $itemTaxes = [];
        $orderTax = $write->query("SELECT code FROM ".$tableName." WHERE order_id= '".$mageOrderId."'");
        $taxCodeResult = $orderTax->fetch();
        if ($taxCodeResult) {
            $taxCode = $taxCodeResult["code"];
            $erpTaxId = $this->getOdooTaxId($taxCode);
            if ($erpTaxId) {
                array_push($itemTaxes, new xmlrpcval($erpTaxId, "int"));
            }
        }
        return $itemTaxes;
    }

    public function createOdooOrderLineVoucherLine($thisOrder, $discountAmount, $odooId, $taxes, $productName)
    {
        $voucherLineId = 0;
        
        $discountAmount = -(float)$discountAmount;

        $description = "Discount";
        $name = "Discount on ".$productName;
        $voucherLineArray =  [
                'order_id'=>new xmlrpcval($odooId, "int"),
                'name'=>new xmlrpcval(urlencode($name), "string"),
                'price_unit'=>new xmlrpcval($discountAmount, "double"),
                'description'=>new xmlrpcval(urlencode($description), "string")
            ];
        if ($taxes) {
            $voucherLineArray['tax_id'] = $taxes;
        }
        $voucherLineId = $this->syncExtraOdooOrderLine($thisOrder, $voucherLineArray, $description);

        return $voucherLineId;
    }

    public function createOdooOrderVoucherLine($thisOrder, $odooId)
    {
        $voucherLineId = 0;
        $incrementId = $thisOrder->getIncrementId();
        $helper = $this->_connection;
        $userId = $helper->getSession()->getUserId();
        $client = $helper->getClientConnect();
        $context = $helper->getOdooContext();
        
        $discountAmount = $thisOrder->getDiscountAmount();

        $description = "Discount";
        $name = "Discount";
        $couponDesc = $thisOrder->getDiscountDescription();
        if ($couponDesc) {
            $description .= "-".$couponDesc;
        }
        $code = $thisOrder->getCouponCode();
        if ($code) {
            $name = "Voucher";
            $description .= " Coupon Code:-".$code;
        }
        
        $voucherLineArray =  [
                'order_id'=>new xmlrpcval($odooId, "int"),
                'name'=>new xmlrpcval(urlencode($name), "string"),
                'price_unit'=>new xmlrpcval($discountAmount, "double"),
                'description'=>new xmlrpcval(urlencode($description), "string")
            ];
        $voucherLineId = $this->syncExtraOdooOrderLine($thisOrder, $voucherLineArray, $description);

        return $voucherLineId;
    }

    public function createOdooOrderShippingLine($thisOrder, $odooId)
    {
        $mageOrderId = $thisOrder->getId();
        $shippingDescription = urlencode($thisOrder->getShippingDescription());
        $shippingLineArray =  [
                'order_id'=>new xmlrpcval($odooId, "int"),
                'name'=>new xmlrpcval('Shipping', "string"),
                'description'=>new xmlrpcval($shippingDescription, "string")
            ];
        $shippingIncludesTax = $this->_scopeConfig->getValue('tax/calculation/shipping_includes_tax');
        if ($shippingIncludesTax) {
            $shippingLineArray['price_unit'] = new xmlrpcval($thisOrder->getShippingInclTax(), "double");
        } else {
            $shippingLineArray['price_unit'] = new xmlrpcval($thisOrder->getShippingAmount(), "double");
        }
        if ($thisOrder->getShippingTaxAmount()>0) {
            $shippingTaxes = $this->getMagentoTaxId($mageOrderId, 'shipping');
            if ($shippingTaxes) {
                $shippingLineArray['tax_id'] = new xmlrpcval($shippingTaxes, "array");
            }
        }

        $shippingLineId = $this->syncExtraOdooOrderLine($thisOrder, $shippingLineArray, $shippingDescription);

        return $shippingLineId;
    }

    public function getMagentoTaxId($orderId, $taxType)
    {
        $taxItems = $this->_taxItemModel
                         ->getTaxItemsByOrderId($orderId);
        $odooTaxes = [];
        foreach ($taxItems as $value) {
            if (isset($value['taxable_item_type'])) {
                if ($value['taxable_item_type'] == $taxType) {
                    if (isset($value['code'])) {
                        $erpTaxId = $this->getOdooTaxId($value['code']);
                        array_push($odooTaxes, new xmlrpcval($erpTaxId, "int"));
                    }
                }
            }
        }
        return $odooTaxes;
    }

    public function syncExtraOdooOrderLine($thisOrder, $extraLineArray, $type = "Extra")
    {
        $extraLineId = '';
        $incrementId = $thisOrder->getIncrementId();
        $helper = $this->_connection;
        $userId = $helper->getSession()->getUserId();
        $context = $helper->getOdooContext();
        $client = $helper->getClientConnect();
        $extraLineArray['ecommerce_channel'] = new xmlrpcval("magento", "string");
        $msg = new xmlrpcmsg('execute');
        $msg->addParam(new xmlrpcval($helper::$odooDb, "string"));
        $msg->addParam(new xmlrpcval($userId, "int"));
        $msg->addParam(new xmlrpcval($helper::$odooPwd, "string"));
        $msg->addParam(new xmlrpcval("bridge.backbone", "string"));
        $msg->addParam(new xmlrpcval("extra_order_line", "string"));
        $msg->addParam(new xmlrpcval($extraLineArray, "struct"));
        $msg->addParam(new xmlrpcval($context, "struct"));
        $resp = $client->send($msg);
        if ($resp->faultCode()) {
            $error = $type." Line Export Error, For Order ".$incrementId." >>".$resp->faultString();
            $helper->addError($error);
        } else {
            $extraLineId = $resp->value()->me['int'];
            $extraLineId = $extraLineId.",";
        }
        return $extraLineId;
    }

    public function createOrderMapping($thisOrder, $odooId, $partnerId, $lineids = '')
    {
        $mageOrderId = $thisOrder->getId();
        $incrementId = $thisOrder->getIncrementId();
        $helper = $this->_connection;
        $userId = $helper->getSession()->getUserId();
        $client = $helper->getClientConnect();
        $context = $helper->getOdooContext();
        $idList[0]= new xmlrpcval($odooId, 'int');
        $key = array(
                new xmlrpcval('name', 'string')
            );
        $msg = new xmlrpcmsg('execute');
        $msg->addParam(new xmlrpcval($helper::$odooDb, "string"));
        $msg->addParam(new xmlrpcval($userId, "int"));
        $msg->addParam(new xmlrpcval($helper::$odooPwd, "string"));
        $msg->addParam(new xmlrpcval("sale.order", "string"));
        $msg->addParam(new xmlrpcval("read", "string"));
        $msg->addParam(new xmlrpcval($idList, "array"));
        $msg->addParam(new xmlrpcval($key, "array"));
        $resp = $client->send($msg);
        if ($resp->faultCode()) {
            $error = "Failed To Read Odoo Order Id = ".$odooId." >>".$resp->faultString();
			$helper->addError($error);
        } else {
            $val = $resp->value();
            $ids = $val->scalarval();
            if ($ids) {
                $orderName = $ids[0]->me["struct"]["name"]->me["string"];

                $mappingData = array(
                        'magento_order'=>$incrementId,
                        'odoo_id'=>$odooId,
                        'odoo_customer_id'=>$partnerId,
                        'magento_id'=>$mageOrderId,
                        'odoo_line_id'=>rtrim($lineids, ","),
                        'odoo_order'=>$orderName,
                        'created_by'=>$helper::$mageUser,
                    );
                $this->createMapping($mappingData);
                
                /* Mapping Entry At Odoo End*/
                $mappingArray = array(
                        'order_ref'=>new xmlrpcval($odooId, "int"),
                        'oe_order_id'=>new xmlrpcval($odooId, "int"),
                        'mage_increment_id'=>new xmlrpcval($incrementId, "string"),
                        'instance_id'=>$context['instance_id'],
                    );
                $orderMap = new xmlrpcmsg('execute');
                $orderMap->addParam(new xmlrpcval($helper::$odooDb, "string"));
                $orderMap->addParam(new xmlrpcval($userId, "int"));
                $orderMap->addParam(new xmlrpcval($helper::$odooPwd, "string"));
                $orderMap->addParam(new xmlrpcval("magento.orders", "string"));
                $orderMap->addParam(new xmlrpcval("create", "string"));
                $orderMap->addParam(new xmlrpcval($mappingArray, "struct"));
                $orderMap->addParam(new xmlrpcval($context, "struct"));
                $orderMapResp = $client->send($orderMap);
                return true;
            }
        }
    }

    public function confirmOdooOrder($odooId)
    {
        $helper = $this->_connection;
        $helper->getSocketConnect();
        $client = $helper->getClientConnect();
        $context = $helper->getOdooContext();
        $userId = $helper->getSession()->getUserId();
        $method = new xmlrpcmsg('exec_workflow');
        $method->addParam(new xmlrpcval($helper::$odooDb, "string"));
        $method->addParam(new xmlrpcval($userId, "int"));
        $method->addParam(new xmlrpcval($helper::$odooPwd, "string"));
        $method->addParam(new xmlrpcval("sale.order", "string"));
        $method->addParam(new xmlrpcval("order_confirm", "string"));
        $method->addParam(new xmlrpcval($odooId, "int"));
        $resp = $client->send($method);
        if ($resp->faultcode()) {
            $error = "Odoo Order ".$odooId." Error During Order Confirm >>".$resp->faultString();
            $helper->addError($error);
        }
    }

    public function invoiceOdooOrder($thisOrder, $odooId, $invoiceNumber, $partnerId=false)
    {
        $helper = $this->_connection;
        $helper->getSocketConnect();
        $client = $helper->getClientConnect();
        $context = $helper->getOdooContext();
        $userId = $helper->getSession()->getUserId();
        
        $invoiceDate = $thisOrder->getUpdatedAt();
        $incrementId = $thisOrder->getIncrementId();
        $invoice = $thisOrder->getInvoiceCollection()->getData();
        foreach ($invoice as $inv) {
            $invoiceDate = $inv['created_at'];
            if (!$invoiceNumber) {
                $invoiceNumber = $inv['increment_id'];
            }
            break;
        }
        $invoiceArray = array(
            'order_id'=>new xmlrpcval($odooId, "int"),
            'date'=>new xmlrpcval($invoiceDate, "string"),
            'mage_inv_number'=>new xmlrpcval($invoiceNumber, "string")
        );
        $msg = new xmlrpcmsg('execute');
        $msg->addParam(new xmlrpcval($helper::$odooDb, "string"));
        $msg->addParam(new xmlrpcval($userId, "int"));
        $msg->addParam(new xmlrpcval($helper::$odooPwd, "string"));
        $msg->addParam(new xmlrpcval("bridge.backbone", "string"));
        $msg->addParam(new xmlrpcval("create_order_invoice", "string"));
        $msg->addParam(new xmlrpcval($invoiceArray, "struct"));
        $msg->addParam(new xmlrpcval($context, "struct"));
        $resp = $client->send($msg);

        if ($resp->faultcode()) {
            $error = "Sync Error, Order ".$incrementId." During Invoice >>".$resp->faultString();
            $helper->addError($error);
            return false;
        } else {
            $resValue = $resp->value()->me;
            if (array_key_exists('int', $resValue)){
                $invoiceId = $resp->value()->me["int"];
            }
            else {
                return false;
            }
            if ($invoiceId > 0) {
                /**
                ******** Odoo Order Payment *************
                */
                $paymentMethod = $thisOrder->getPayment()->getMethodInstance()->getTitle();
                
                $journalId = $this->getOdooPaymentMethod($paymentMethod);
                $paymentArray = [
                            'partner_id'=>new xmlrpcval($partnerId, "int"),
                            'reference'=>new xmlrpcval($incrementId, "string"),
                            'invoice_id'=>new xmlrpcval($invoiceId, "int"),
                            'journal_id'=>new xmlrpcval($journalId, "int")
                        ];

                $payment = new xmlrpcmsg('execute');
                $payment->addParam(new xmlrpcval($helper::$odooDb, "string"));
                $payment->addParam(new xmlrpcval($userId, "int"));
                $payment->addParam(new xmlrpcval($helper::$odooPwd, "string"));
                $payment->addParam(new xmlrpcval("bridge.backbone", "string"));
                $payment->addParam(new xmlrpcval("sales_order_payment", "string"));
                $payment->addParam(new xmlrpcval($paymentArray, "struct"));
                $payResp = $client->send($payment);
                if ($payResp->faultcode()) {
                    $error = "Sync Error, Order ".$incrementId." During Payment >>".$payResp->faultString();
                    $helper->addError($error);
                    return false;
                }
            }
        }
        return true;
    }

    public function deliverOdooOrder($thisOrder, $erpOrderId, $shipmentObj = false)
    {
        $shipmentNo = false;
        $tracknums = false;
        $trackCarrier = false;
        $helper = $this->_connection;
        $client = $helper->getClientConnect();
        $context = $helper->getOdooContext();
        $userId = $helper->getSession()->getUserId();
        $incrementId = $thisOrder->getIncrementId();
        if ($shipmentObj) {
            if (count($shipmentObj)) {
                $shipmentNo = $shipmentObj->getId();
                foreach ($shipmentObj->getAllTracks() as $tracknum) {
                    $tracknums=$tracknum->getTrackNumber();
                    $trackCarrier=$tracknum->getCarrierCode();
                    break;
                }
            }
        } else {
            $shipment = $thisOrder->getShipmentsCollection();
            foreach ($shipment as $ship) {
                $shipmentNo = $ship->getId();
                foreach ($ship->getAllTracks() as $tracknum) {
                    $tracknums=$tracknum->getTrackNumber();
                    $trackCarrier=$tracknum->getCarrierCode();
                    break;
                }
                break;
            }
        }
        $shipmentArray = array(
            'order_id'=>new xmlrpcval($erpOrderId, "int"),
            'mage_ship_number'=>new xmlrpcval($shipmentNo, "string"),
            'carrier_tracking_ref'=>new xmlrpcval($tracknums, "string"),
            'carrier_code'=>new xmlrpcval($trackCarrier, "string"),
        );
        $msg = new xmlrpcmsg('execute');
        $msg->addParam(new xmlrpcval($helper::$odooDb, "string"));
        $msg->addParam(new xmlrpcval($userId, "int"));
        $msg->addParam(new xmlrpcval($helper::$odooPwd, "string"));
        $msg->addParam(new xmlrpcval("bridge.backbone", "string"));
        $msg->addParam(new xmlrpcval("order_shipped", "string"));
        $msg->addParam(new xmlrpcval($shipmentArray, "struct"));
        $msg->addParam(new xmlrpcval($context, "struct"));
        $resp = $client->send($msg);
        if ($resp->faultcode()) {
            $error = "Sync Error, Order ".$incrementId." During Shipment >>".$resp->faultString();
            $helper->addError($error);
            return false;
        }
        return true;
    }

    public function getOdooPaymentMethod($paymentMethod)
    {
        $mappingcollection = $this->_paymentModel
                                    ->getCollection()
                                    ->addFieldToFilter('magento_id', $paymentMethod);
        if (count($mappingcollection) > 0) {
            foreach ($mappingcollection as $map) {
                return $map->getOdooId();
            }
        } else {
            $response = $this->_paymentMapping
                             ->syncSpecificPayment($paymentMethod);
            $erpPaymentId = $response['odoo_id'];
            return $erpPaymentId;
        }
    }

    public function getErpOrderAddresses($thisOrder)
    {
        $partnerId = 0;
        $partnerInvoiceId = 0;
        $partnerShippingId = 0;
        $storeId = $thisOrder->getStoreId();
        $magerpsync = $this->_customerMapping;
        $billing = $thisOrder->getBillingAddress();
        $shipping = $thisOrder->getShippingAddress();
        if ($billing) {
            $billing->setEmail($thisOrder->getCustomerEmail());
        }
        if ($shipping) {
            $shipping->setEmail($thisOrder->getCustomerEmail());
        }
        if ($thisOrder->getCustomerIsGuest() == 1) {
            $customerArray =  [
                        'name'=>new xmlrpcval(urlencode($billing->getName()), "string"),
                        'email'=>new xmlrpcval(urlencode($thisOrder->getCustomerEmail()), "string"),
                        'is_company'=>new xmlrpcval(false, "boolean"),
                    ];
            $partnerId = $magerpsync->odooCustomerCreate($customerArray, 0, 0, $storeId);

            $isDifferent = $this->checkAddresses($thisOrder);
            if ($isDifferent == true) {
                $partnerShippingId = $this->createErpAddress($shipping, $partnerId, 0, 0, $storeId);
                $partnerInvoiceId = $this->createErpAddress($billing, $partnerId, 0, 0, $storeId);
            } else {
                $partnerInvoiceId = $this->createErpAddress($billing, $partnerId, 0, 0, $storeId);
                $partnerShippingId = $partnerInvoiceId;
            }
        }
        $customerId = $thisOrder->getCustomerId();
        if ($customerId > 0) {
            $mappingcollection = $this->_customerModel
                                        ->getCollection()
                                        ->addFieldToFilter('magento_id', ['eq'=>$customerId])
                                        ->addFieldToFilter('address_id', ['eq'=>"customer"]);

            if (count($mappingcollection)>0) {
                foreach ($mappingcollection as $map) {
                    $partnerId = $map->getOdooId();
                    $mapNeedSync = $map->getNeedSync();
                }

                $isDifferent = $this->checkAddresses($thisOrder);
                $billingAddresssId =  $billing->getCustomerAddressId();
                if ($isDifferent == true) {
                    $shippingAddressId = $shipping->getCustomerAddressId();
                    $partnerShippingId = $this->createErpAddress(
                        $shipping,
                        $partnerId,
                        $customerId,
                        $shippingAddressId,
                        $storeId
                    );
                    
                    $partnerInvoiceId = $this->createErpAddress($billing, $partnerId, $customerId, $billingAddresssId, $storeId);
                } else {
                    $partnerInvoiceId = $this->createErpAddress($billing, $partnerId, $customerId, $billingAddresssId, $storeId);
                    $partnerShippingId = $partnerInvoiceId;
                }
            } else {
                $customerArray =  [
                        'name'=>new xmlrpcval(urlencode($thisOrder->getCustomerName()), "string"),
                        'email'=>new xmlrpcval(urlencode($thisOrder->getCustomerEmail()), "string"),
                        'is_company'=>new xmlrpcval(false, "boolean"),
                    ];
                $partnerId = $magerpsync->odooCustomerCreate($customerArray, $customerId, 'customer', $storeId);

                $isDifferent = $this->checkAddresses($thisOrder);
                $billingAddresssId =  $billing->getCustomerAddressId();
                if ($isDifferent == true) {
                    $shippingAddressId = $shipping->getCustomerAddressId();
                    $partnerShippingId = $this->createErpAddress(
                        $shipping,
                        $partnerId,
                        $customerId,
                        $shippingAddressId,
                        $storeId
                    );
                    
                    $partnerInvoiceId = $this->createErpAddress($billing, $partnerId, $customerId, $billingAddresssId, $storeId);
                } else {
                    $partnerInvoiceId = $this->createErpAddress($billing, $partnerId, $customerId, $billingAddresssId, $storeId);
                    $partnerShippingId = $partnerInvoiceId;
                }
            }
        }

        return [$partnerId, $partnerInvoiceId, $partnerShippingId];
    }

    public function createErpAddress($flatAddress, $parentId, $mageCustomerId, $mageAddressId, $storeId = 0)
    {
        $flag = false;
        $erpCusId = 0;
        $addressArray = [];
        $addressArray = $this->customerAddressArray($flatAddress);
       
        if ($mageAddressId > -1) {
            $addresscollection =  $this->_customerModel
                                        ->getCollection()
                                        ->addFieldToFilter('magento_id', ['eq'=>$mageCustomerId])
                                        ->addFieldToFilter('address_id', ['eq'=>$mageAddressId]);

            if (count($addresscollection)>0) {
                foreach ($addresscollection as $add) {
                    $mapId = $add->getEntityId();
                    $erpCusId = $add->getOdooId();
                }
            } else {
                $flag = true;
            }
        } else {
            $flag = true;
        }
        if ($flag == true) {
            if ($addressArray) {
                $addressArray['parent_id'] = new xmlrpcval($parentId, "int");
                $erpCusId = $this->_customerMapping
                                ->odooCustomerCreate($addressArray, $mageCustomerId, $mageAddressId, $storeId);
            }
        }
        return $erpCusId;
    }

    public function customerAddressArray($flatAddress)
    {
        $type = '';
        $addressArray = [];
        if ($flatAddress['address_type'] == 'billing') {
            $type = 'invoice';
        }
        if ($flatAddress['address_type'] == 'shipping') {
            $type = 'delivery';
        }
        $streets = $flatAddress->getStreet();
        if (count($streets)>1) {
            $street = urlencode($streets[0]);
            $street2 = urlencode($streets[1]);
        } else {
            $street = urlencode($streets[0]);
            $street2 = urlencode('');
        }
        $name = urlencode($flatAddress->getName());
        $company = urlencode($flatAddress->getCompany());
        $email = urlencode($flatAddress->getEmail());
        $city = urlencode($flatAddress->getCity());
        $region = urlencode($flatAddress->getRegion());

        $addressArray =  [
            'name'=>new xmlrpcval($name, "string"),
            'street'=>new xmlrpcval($street, "string"),
            'street2'=>new xmlrpcval($street2, "string"),
            'city'=>new xmlrpcval($city, "string"),
            'email'=>new xmlrpcval($email, "string"),
            'zip'=>new xmlrpcval(urlencode($flatAddress->getPostcode()), "string"),
            'phone'=>new xmlrpcval($flatAddress->getTelephone(), "string"),
            'fax'=>new xmlrpcval($flatAddress->getFax(), "string"),
            'country_code'=>new xmlrpcval($flatAddress->getCountryId(), "string"),
            'region'=>new xmlrpcval($region, "string"),
            'wk_company'=>new xmlrpcval($company, "string"),
            'customer'=>new xmlrpcval(false, "boolean"),
            'wk_address'=>new xmlrpcval(true, "boolean"),
            'type'=>new xmlrpcval($type, "string")
        ];
        return $addressArray;
    }

    public function checkAddresses($thisOrder)
    {
        $flag = false;
        if ($thisOrder->getShippingAddressId() && $thisOrder->getBillingAddressId()) {
            $s = $thisOrder->getShippingAddress();
            $b = $thisOrder->getBillingAddress();
            if ($s['street'] != $b['street']) {
                $flag = true;
            }
            if ($s['postcode'] != $b['postcode']) {
                $flag = true;
            }
            if ($s['city'] != $b['city']) {
                $flag = true;
            }
            if ($s['region'] != $b['region']) {
                $flag = true;
            }
            if ($s['country_id'] != $b['country_id']) {
                $flag = true;
            }
            if ($s['firstname'] != $b['firstname']) {
                $flag = true;
            }
        }
        return $flag;
    }

    public function createMapping($data)
    {
        $createdBy = 'Magento';
        if (isset($data['created_by'])) {
            $createdBy = $data['created_by'];
        }
        $carrierModel = $this->_objectManager->create('Webkul\Odoomagentoconnect\Model\Order');
        $carrierModel->setData($data);
        $carrierModel->save();
        return true;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('odoomagentoconnect_order', 'entity_id');
    }
}
