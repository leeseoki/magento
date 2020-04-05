<?php
namespace Webkul\Odoomagentoconnect\Api\Data;

/**
 * Webkul Odoomagentoconnect Payment Interface
 * @category  Webkul
 * @package   Webkul_Odoomagentoconnect
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

interface PaymentInterface
{
   /**
    * Constants for keys of data array. Identical to the name of the getter in snake case
    */
    const MAGENTO_ID    = 'magento_id';
    const ODOO_ID       = 'odoo_id';
    const ODOO_PAYMENT_ID      = 'odoo_payment_id';
    const ODOO_WORKFLOW_ID      = 'odoo_workflow_id';
    const CREATED_BY    = 'created_by';
    const CREATED_AT    = 'created_at';

    /**
     * Get the magentoId.
     *
     * @api
     * @return int|null
     */
    public function getMagentoId();

    /**
     * Get odooId
     *
     * @return int|null
     */
    public function getOdooId();

     /**
     * Get odooPaymentId
     *
     * @return int|null
     */
    public function getOdooPaymentId();

     /**
     * Get odooWorkflowId
     *
     * @return int|null
     */
    public function getOdooWorkflowId();

    /**
     * Get Created By
     *
     * @return string|null
     */
    public function getCreatedBy();

    /**
     * Set magentoId
     *
     * @api
     * @param int $magentoId
     * @return $this
     */
    public function setMagentoId($magentoId);

    /**
     * Set odooId
     *
     * @api
     * @param int $odooId
     * @return $this
     */
    public function setOdooId($odooId);

     /**
     * Set odooPaymentId
     *
     * @api
     * @param int $odooPaymentId
     * @return $this
     */
    public function setOdooPaymentId($odooPaymentId);

     /**
     * Set odooWorkflowId
     *
     * @api
     * @param int $odooWorkflowId
     * @return $this
     */
    public function setOdooWorkflowId($odooWorkflowId);

    /**
     * Set createdBy
     *
     * @api
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy);
}
