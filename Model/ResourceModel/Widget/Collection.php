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

namespace Tawk\Widget\Model\ResourceModel\Widget;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Tawk\Widget\Model\ResourceModel\Widget;
use Tawk\Widget\Model\Widget as WidgetModel;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(WidgetModel::class, Widget::class);
    }
}
