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

namespace Tawk\Widget\Model;

use \Magento\Framework\Model\AbstractModel;
use Tawk\Widget\Model\ResourceModel\Widget as WidgetResourceModel;

class Widget extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(WidgetResourceModel::class);
    }

    public function loadByForStoreId($id)
    {
        return $this->getCollection()
            ->addFieldToFilter('for_store_id', $id)
            ->getFirstItem();
    }
}
