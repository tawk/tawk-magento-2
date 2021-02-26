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
use Tawk\Widget\Model\WidgetFactory;
use Magento\Customer\Model\SessionFactory;

class Embed extends Template
{
    const TAWK_EMBED_URL = 'https://embed.tawk.to';
    protected $modelWidgetFactory;
    protected $logger;
    protected $model;
    protected $storeManager;
    protected $request;
    protected $modelSessionFactory;

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
    }

    public function getEmbedUrl()
    {
        return self::TAWK_EMBED_URL.'/'.$this->model->getPageId().'/'.$this->model->getWidgetId();
    }

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
            if (strlen($excluded_url_list) > 0) {
                $current_url = $httpHost . $requestUri;
                $current_url = urldecode($current_url);

                $ssl = !empty($httpsServer) && $httpsServer == 'on';
                $sp = strtolower($serverProtocol);
                $protocol = substr($sp, 0, strpos($sp, '/')) . ($ssl ? 's' : '');

                $current_url = $protocol.'://'.$current_url;
                $current_url = strtolower($current_url);
                $current_url = trim(strtolower($current_url));

                $excluded_url_list = preg_split("/,/", $excluded_url_list);
                foreach ($excluded_url_list as $exclude_url) {
                    $exclude_url = strtolower(urldecode(trim($exclude_url)));
                    if (strpos($current_url, $exclude_url) !== false) {
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
            if (strlen($included_url_list) > 0) {
                $current_url = $httpHost . $requestUri;
                $current_url = urldecode($current_url);

                $ssl = (!empty($httpsServer) && $httpsServer == 'on');
                $sp = strtolower($serverProtocol);
                $protocol = substr($sp, 0, strpos($sp, '/')) . ($ssl ? 's' : '');

                $current_url = $protocol.'://'.$current_url;
                $current_url = strtolower($current_url);
                $current_url = trim(strtolower($current_url));

                $included_url_list = preg_split("/,/", $included_url_list);
                foreach ($included_url_list as $include_url) {
                    $include_url = strtolower(urldecode(trim($include_url)));
                    if (strpos($current_url, $include_url) !== false) {
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
}
