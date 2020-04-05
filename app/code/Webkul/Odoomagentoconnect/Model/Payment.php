<?php
/**
 * Webkul Odoomagentoconnect Payment Model
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Odoomagentoconnect\Model;

use Webkul\Odoomagentoconnect\Api\Data\PaymentInterface;
use Magento\Framework\DataObject\IdentityInterface;

class Payment extends \Magento\Framework\Model\AbstractModel implements PaymentInterface, IdentityInterface
{
    protected $_interfaceAttributes = [

    PaymentInterface::MAGENTO_ID,
    PaymentInterface::ODOO_PAYMENT_ID,
    PaymentInterface::ODOO_WORKFLOW_ID,
    PaymentInterface::ODOO_ID,
    PaymentInterface::CREATED_BY,
    ];

    /**#@+
     * Post's Statuses
     */
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 0;
    /**#@-*/

    /**
     * CMS page cache tag
     */
    const CACHE_TAG = 'odoomagentoconnect_payment';

    /**
     * @var string
     */
    protected $_cacheTag = 'odoomagentoconnect_payment';

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'odoomagentoconnect_payment';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Webkul\Odoomagentoconnect\Model\ResourceModel\Payment');
    }
    /**
     * Prepare post's statuses.
     * Available event to customize statuses.
     *
     * @return array
     */
    public function getAvailableStatuses()
    {
        return [self::STATUS_ENABLED => __('Enabled'), self::STATUS_DISABLED => __('Disabled')];
    }
    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get the magento_id.
     *
     * @api
     * @return int|null
     */

    public function getMagentoId()
    {
        return $this->getData(self::MAGENTO_ID);
    }

    /**
     * Get the odoo_id.
     *
     * @api
     * @return int|null
     */

    public function getOdooId()
    {
        return $this->getData(self::ODOO_ID);
    }

    /**
     * Get the odoo_payment_id.
     *
     * @api
     * @return int|null
     */

    public function getOdooPaymentId()
    {
        return $this->getData(self::ODOO_PAYMENT_ID);
    }

    /**
     * Get the odoo_workflow_id.
     *
     * @api
     * @return int|null
     */

    public function getOdooWorkflowId()
    {
        return $this->getData(self::ODOO_WORKFLOW_ID);
    }

    /**
     * Get the created_by.
     *
     * @api
     * @return string|null
     */

    public function getCreatedBy()
    {
        return $this->getData(self::CREATED_BY);
    }

    /**
     * Set magentoId
     *
     * @api
     * @param int $magentoId
     * @return $this
     */

    public function setMagentoId($magentoId)
    {
        return $this->setData(self::MAGENTO_ID, $magentoId);
    }

    /**
     * Set odooId
     *
     * @api
     * @param int $odooId
     * @return $this
     */
    public function setOdooId($odooId)
    {
        return $this->setData(self::ODOO_ID, $odooId);
    }

    /**
     * Set odooPaymentId
     *
     * @api
     * @param int $odooPaymentId
     * @return $this
     */
    public function setOdooPaymentId($odooPaymentId)
    {
        return $this->setData(self::ODOO_PAYMENT_ID, $odooPaymentId);
    }

      /**
     * Set odooWorkflowId
     *
     * @api
     * @param int $odooWorkflowId
     * @return $this
     */
    public function setOdooWorkflowId($odooWorkflowId)
    {
        return $this->setData(self::ODOO_WORKFLOW_ID, $odooWorkflowId);
    }

    /**
     * Set createdBy
     *
     * @api
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy)
    {
        return $this->setData(self::CREATED_BY, $createdBy);
    }
}
