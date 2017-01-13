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

namespace Tawk\Widget\Controller\Adminhtml\RemoveWidget;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;

class Index extends \Magento\Backend\App\Action
{
    protected $resultJsonFactory;
    protected $logger;

    public function __construct(Context $context, JsonFactory $resultJsonFactory, LoggerInterface $logger)
    {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
    }

    public function execute()
    {
        $response = $this->resultJsonFactory->create();
        $response->setHeader('Content-type', 'application/json');
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $objectManager->get('Tawk\Widget\Model\Widget')->loadByForStoreId(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING))->delete();

        return $response->setData(['success' => TRUE]);
    }
}