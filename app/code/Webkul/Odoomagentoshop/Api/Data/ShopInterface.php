<?php
namespace Webkul\Odoomagentoshop\Api\Data;

/**
 * Webkul Odoomagentoshop Shop Interface
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

interface ShopInterface
{
/**
    * Constants for keys of data array. Identical to the name of the getter in snake case
    */
    const MAGENTO_ID    = 'magento_id';
    const ODOO_ID       = 'odoo_id';
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
     * Set createdBy
     *
     * @api
     * @param string $createdBy
     * @return $this
     */
    public function setCreatedBy($createdBy);
}
