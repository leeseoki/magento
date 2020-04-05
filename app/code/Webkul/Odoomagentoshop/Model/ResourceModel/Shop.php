<?php

namespace Webkul\Odoomagentoshop\Model\ResourceModel;

/**
 * Webkul Odoomagentoshop Shop
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2016 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

use Webkul\Odoomagentoconnect\Helper\Connection;
use xmlrpc_client;
use xmlrpcval;
use xmlrpcmsg;

/**
 * Blog post mysql resource
 */
class Shop extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        $resourcePrefix = null
    ) {
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_logger = $logger;
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('odoomagentoshop_shop', 'entity_id');
    }
}
