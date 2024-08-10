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

namespace Tawk\Widget\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\SessionFactory;

use Tawk\Modules\UrlPatternMatcher;
use Tawk\Widget\Model\WidgetFactory;


class Embed extends Template
{
    /**
     * Tawk.to Widget Model instance
     *
     * @var WidgetFactory $modelWidgetFactory
     */
    protected $modelWidgetFactory;

    /**
     * Logger instance
     *
     * @var \Psr\Log\LoggerInterface $logger
     */
    protected $logger;

    /**
     * Tawk.to Widget Model Data Object instance
     *
     * @var \Magento\Framework\DataObject $model
     */
    protected $model;

    /**
     * Store Manager instance
     *
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    protected $storeManager;

    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface $request
     */
    protected $request;

    /**
     * Session Factory instance
     *
     * @var SessionFactory $modelSessionFactory
     */
    protected $modelSessionFactory;

    /**
     * Layout instance
     *
     * @var \Magento\Framework\View\LayoutInterface $layout
     */
    protected $layout;

    /**
     * Constructor
     *
     * @param SessionFactory $sessionFactory Session Factory instance
     * @param WidgetFactory $modelFactory Tawk.to Widget Model instance
     * @param Template\Context $context Template Context
     * @param array $data Template data
     */
    public function __construct(
        SessionFactory $sessionFactory,
        WidgetFactory $modelFactory,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->modelWidgetFactory = $modelFactory->create();
        $this->storeManager = $context->getStoreManager();
        $this->logger = $context->getLogger();
        $this->model = $this->getWidgetModel();
        $this->request = $context->getRequest();
        $this->modelSessionFactory = $sessionFactory->create();
        $this->layout = $context->getLayout();
    }

    /**
     * Retrieves embed url.
     *
     * @return string Embed Url.
     */
    public function getEmbedUrl()
    {
        return 'https://embed.tawk.to'.
            '/'.htmlspecialchars($this->model->getPageId()).
            '/'.htmlspecialchars($this->model->getWidgetId());
    }

    /**
     * Instantiate widget model data object
     *
     * @return \Magento\Framework\DataObject|null Returns `DataObject` if model is found. Otherwise, returns `null`.
     */
    private function getWidgetModel()
    {
        $store = $this->storeManager->getStore();

        $storeId   = $store->getId();
        $groupId   = $store->getGroup()->getId();
        $websiteId = $store->getWebsite()->getId();

        //order in which we select widget
        $ids = [$websiteId.'_'.$groupId.'_'.$storeId, $websiteId.'_'.$groupId, $websiteId, 'global'];

        foreach ($ids as $id) {
            $tmpModel = $this->modelWidgetFactory->loadByForStoreId($id);

            if ($tmpModel->hasId()) {
                return $tmpModel;
            }
        }

        return null;
    }

    /**
     * Retrieves current customer details.
     *
     * @return array {
     *   name: string,
     *   email: string
     * }
     */
    public function getCurrentCustomerDetails()
    {
        if ($this->model->getEnableVisitorRecognition() != 1) {
            return null;
        }

        if (!$this->modelSessionFactory->isLoggedIn()) {
            return null;
        }

        $customerSession = $this->modelSessionFactory->getCustomer();
        return [
            'name'  => $customerSession->getName(),
            'email' => $customerSession->getEmail()
        ];
    }

    /**
     * To or to not display the selected widget.
     */
    protected function _toHtml()
    {
        if ($this->model === null) {
            return '';
        }

        $alwaysdisplay = $this->model->getAlwaysDisplay();
        $donotdisplay = $this->model->getDoNotDisplay();
        $display = true;

        $httpHost = $this->request->getServer('HTTP_HOST');
        $requestUri = $this->request->getServer('REQUEST_URI');
        $httpsServer = $this->request->getServer('HTTPS');
        $serverProtocol = $this->request->getServer('SERVER_PROTOCOL');

        if ($alwaysdisplay == 1) {
            $display = true;
            
            $excluded_url_list = $this->model->getExcludeUrl();
            if ($excluded_url_list !== null && strlen($excluded_url_list) > 0) {
                $excluded_handle = $this->checkLayoutHandle($excluded_url_list);
                if ($excluded_handle) {
                    $display = false;
                } else {
                    $current_url = $httpHost . $requestUri;
                    $current_url = urldecode($current_url);

                    $ssl = !empty($httpsServer) && $httpsServer == 'on';
                    $sp = strtolower($serverProtocol);
                    $protocol = substr($sp, 0, strpos($sp, '/')) . ($ssl ? 's' : '');

                    $current_url = $protocol.'://'.$current_url;
                    $current_url = strtolower($current_url);
                    $current_url = trim(strtolower($current_url));

                    $excluded_url_list = explode(PHP_EOL, $excluded_url_list);
                    $excluded_url_list = array_map('trim', $excluded_url_list);
                    if (UrlPatternMatcher::match($current_url, $excluded_url_list)) {
                        $display = false;
                    }
                }
            }
        } else {
            $display = false;
        }

        if ($donotdisplay == 1) {
            $display = false;

            $included_url_list = $this->model->getIncludeUrl();
            if ($included_url_list !== null && strlen($included_url_list) > 0) {
                $included_handle = $this->checkLayoutHandle($included_url_list);
                if ($included_handle) {
                    $display = true;
                } else {
                    $current_url = $httpHost . $requestUri;
                    $current_url = urldecode($current_url);

                    $ssl = (!empty($httpsServer) && $httpsServer == 'on');
                    $sp = strtolower($serverProtocol);
                    $protocol = substr($sp, 0, strpos($sp, '/')) . ($ssl ? 's' : '');

                    $current_url = $protocol.'://'.$current_url;
                    $current_url = strtolower($current_url);
                    $current_url = trim(strtolower($current_url));

                    $included_url_list = explode(PHP_EOL, $included_url_list);
                    $included_url_list = array_map('trim', $included_url_list);
                    if (UrlPatternMatcher::match($current_url, $included_url_list)) {
                        $display = true;
                    }
                }
            }
        }

        if ($display == true) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }

    private function checkLayoutHandle($url_list) {
        $array = explode(PHP_EOL, $url_list);
        if (empty($array)) {
            return false;
        }

        $array = array_map('trim', $array);
        $pattern = '/^[a-z0-9_]+$/';
    
        $checkHandles = array_filter($array, function($item) use ($pattern) {
            return preg_match($pattern, $item);
        });
        if (empty($checkHandles)) {
            return false;
        }

        $existHandle = false;
        $currentPageHandles = $this->layout->getUpdate()->getHandles();
        foreach ($currentPageHandles as $handle) {
            if (in_array($handle, $checkHandles)) {
                $existHandle = true;
                break;
            }
        }

        return $existHandle;
    }
}
