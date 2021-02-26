<?php
/**
 * Tawk.to
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@tawk.to so we can send you a copy immediately.
 *
 * @copyright   Copyright (c) 2016 Tawk.to
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Tawk\Widget\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $table = $setup->getConnection()->newTable($setup->getTable('tawk_widget'))->addColumn(
            'id',
            Table::TYPE_INTEGER,
            10,
            ['identity' => true, 'unsigned' => false, 'nullable' => false, 'primary' => true],
            'Id'
        )->addColumn(
            'for_store_id',
            Table::TYPE_TEXT,
            50,
            [],
            'For store Id'
        )->addColumn(
            'page_id',
            Table::TYPE_TEXT,
            50,
            [],
            'Page Id'
        )->addColumn(
            'widget_id',
            Table::TYPE_TEXT,
            50,
            [],
            'Widget Id'
        )->addColumn(
            'always_display',
            Table::TYPE_INTEGER,
            1,
            [],
            'always_display'
        )->addColumn(
            'exclude_url',
            Table::TYPE_TEXT,
            255,
            [],
            'exclude_url'
        )->addColumn(
            'do_not_display',
            Table::TYPE_INTEGER,
            1,
            [],
            'do_not_display'
        )->addColumn(
            'include_url',
            Table::TYPE_TEXT,
            255,
            [],
            'include_url'
        )->addColumn(
            'enable_visitor_recognition',
            Table::TYPE_INTEGER,
            1,
            [
                'nullable' => false,
                'default' => 1
            ],
            'Enable visitor recognition feature'
        )->setComment(
            'Tawk Widget table that makes connection between stores and widgets'
        );

        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
