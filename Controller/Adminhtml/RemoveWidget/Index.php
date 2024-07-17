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
use Tawk\Widget\Model\WidgetFactory;

class Index extends \Magento\Backend\App\Action
{
    /**
     * Json Factory instance
     *
     * @var JsonFactory $resultJsonfactory
     */
    protected $resultJsonFactory;

    /**
     * Logger instance
     *
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * Tawk.to Widget Model instance
     *
     * @var WidgetFactory $modelWidgetFactory
     */
    protected $modelWidgetFactory;

    /**
     * Request body
     *
     * @var \Magento\Framework\App\RequestInterface $request
     */
    protected $request;

    /**
     * Constructor
     *
     * @param WidgetFactory $modelFactory Tawk.to Widget Model instance
     * @param Context $context App Context
     * @param JsonFactory $resultJsonFactory Json Factory instance
     * @param LoggerInterface $logger PSR Logger
     */
    public function __construct(
        WidgetFactory $modelFactory,
        Context $context,
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->modelWidgetFactory = $modelFactory->create();
        $this->request = $this->getRequest();
    }

    /**
     * Removes the store's property, widget, and its visibility options
     *
     * @return array {
     *   success: bool
     * }
     */
    public function execute()
    {
        $response = $this->resultJsonFactory->create();
        $response->setHeader('Content-type', 'application/json');

        $storeId = preg_replace("/['\"]/", "", strip_tags($this->request->getParam('id')));
        if (!$storeId) {
            return $response->setData(['success' => false]);
        }

        $this->modelWidgetFactory->loadByForStoreId($storeId)->delete();

        return $response->setData(['success' => true]);
    }
}
