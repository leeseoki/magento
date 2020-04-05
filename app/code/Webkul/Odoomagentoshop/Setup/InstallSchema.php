<?php
/**
 * Webkul Odoomagentoshop Schema Setup
 * @category  Webkul
 * @package   Webkul_Odoomagentoshop
 * @author    Webkul
 * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Odoomagentoshop\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $connection = $setup->getConnection();
        $context = $context;

        $installer->startSetup();

        /**
        * Create table 'odoomagentoshop_shop'
        */
        $table = $connection
            ->newTable($setup->getTable('odoomagentoshop_shop'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity Id'
            )
            ->addColumn(
                'magento_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                255,
                [],
                'Magento Store'
            )
            ->addColumn(
                'odoo_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Odoo Shop'
            )
            ->addIndex(
                $setup->getIdxName('odoomagentoshop_shop', ['group_id']),
                ['magento_id']
            )
            ->addIndex(
                $setup->getIdxName('odoomagentoshop_shop', ['odoo_id']),
                ['odoo_id']
            )
            ->addColumn(
                'created_by',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                [],
                'Created By'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            );

        $connection->createTable($table);

        $installer->endSetup();
    }
}
