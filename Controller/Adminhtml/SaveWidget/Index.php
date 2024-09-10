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
 * @copyright   Copyright (c) 2024 Tawk.to
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Tawk\Widget\Controller\Adminhtml\SaveWidget;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;
use Psr\Log\LoggerInterface;
use Tawk\Widget\Model\WidgetFactory;
use Tawk\Widget\Helper\StringUtil;

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
     * String util helper
     *
     * @var StringUtil $helper
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param WidgetFactory $modelFactory Tawk.to Widget Model instance
     * @param Context $context App Context
     * @param JsonFactory $resultJsonFactory Json Factory instance
     * @param LoggerInterface $logger PSR Logger
     * @param StringUtil $helper String util helper
     */
    public function __construct(
        WidgetFactory $modelFactory,
        Context $context,
        JsonFactory $resultJsonFactory,
        LoggerInterface $logger,
        StringUtil $helper
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->modelWidgetFactory = $modelFactory->create();
        $this->request = $this->getRequest();
        $this->helper = $helper;
    }

    /**
     * Saves selected property, widget, and its visibility options.
     *
     * @return array {
     *   success: bool
     * }
     */
    public function execute()
    {
        $response = $this->resultJsonFactory->create();
        $response->setHeader('Content-type', 'application/json');

        $pageId = $this->helper->stripTagsandQuotes($this->request->getParam('pageId'));
        $widgetId = $this->helper->stripTagsandQuotes($this->request->getParam('widgetId'));
        $storeId = $this->helper->stripTagsandQuotes($this->request->getParam('id'));

        if (!$pageId || !$widgetId || !$storeId) {
            return $response->setData(['success' => false]);
        }

        $alwaysdisplay = filter_var($this->request->getParam('alwaysdisplay'), FILTER_SANITIZE_NUMBER_INT);
        $excludeurl = $this->request->getParam('excludeurl');
        $donotdisplay = filter_var($this->request->getParam('donotdisplay'), FILTER_SANITIZE_NUMBER_INT);
        $includeurl = $this->request->getParam('includeurl');
        $enableVisitorRecognition = filter_var(
            $this->request->getParam('enableVisitorRecognition'),
            FILTER_SANITIZE_NUMBER_INT
        );

        $model = $this->modelWidgetFactory->loadByForStoreId($storeId);

        if ($pageId != '-1') {
            $model->setPageId($pageId);
        }

        if ($widgetId != '-1') {
            $model->setWidgetId($widgetId);
        }

        $model->setForStoreId($storeId);

        $model->setAlwaysDisplay($alwaysdisplay);
        $model->setExcludeUrl($excludeurl);

        $model->setDoNotDisplay($donotdisplay);
        $model->setIncludeUrl($includeurl);

        $model->setEnableVisitorRecognition($enableVisitorRecognition);

        $model->save();

        return $response->setData(['success' => true]);
    }
}
