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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    protected $tableName = 'tawk_widget';
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->versionUpdate150($setup, $context);

        $setup->endSetup();
    }

    private function versionUpdate150($setup, $context)
    {
        if (version_compare($context->getVersion(), '1.5.0', '<')) {
            $setup->getConnection()->addColumn(
                $setup->getTable('tawk_widget'),
                'enable_visitor_recognition',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 1,
                    'nullable' => false,
                    'default' => 1,
                    'comment' => 'Enable visitor recognition feature'
                ]
            );
        }
    }
}
