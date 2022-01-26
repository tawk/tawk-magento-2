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

namespace Tawk\Widget\Block\Adminhtml;

use Magento\Backend\Block\Template;
use Tawk\Widget\Model\WidgetFactory;

class SelectWidgetBlock extends Template
{
    /**
     * Logger instance
     *
     * @var \Psr\Log\LoggerInterface $logger
     */
    protected $logger;

    /**
     * Tawk.to Widget Model instance
     *
     * @var WidgetFactory $modelWidgetFactory
     */
    protected $modelWidgetFactory;

    /**
     * Request instance
     *
     * @var \Magento\Framework\App\RequestInterface $request
     */
    protected $request;

    /**
     * List of valid patterns
     *
     * @var string[]
     */
    private static $validPatternList;

    /**
     * Constructor
     *
     * @param Template\Context $context Template context
     * @param WidgetFactory $modelFactory Tawk.to Widget Model instance
     * @param array $data Template data
     */
    public function __construct(Template\Context $context, WidgetFactory $modelFactory, array $data = [])
    {
        parent::__construct($context, $data);
        $this->logger  = $context->getLogger();
        $this->modelWidgetFactory = $modelFactory;
        $this->request = $context->getRequest();
    }

    /**
     * Retrieves current base url
     *
     * @return string Base url
     */
    public function mainurl()
    {
        $protocol = 'http';
        if ($this->request->isSecure()) {
            $protocol = 'https';
        }
        return $protocol . "://" . $this->request->getServer('HTTP_HOST');
    }

    /**
     * Retrieves the list of valid patterns.
     *
     * @return string[]
     */
    public function getValidPatternList() // Note: static methods are discouraged.
    {
        if (isset(self::$validPatternList) === false) {
            self::$validPatternList = [
                '*',
                '*/to/somewhere',
                '/*/to/somewhere',
                '/path/*/somewhere',
                '/path/*/lead/*/somewhere',
                '/path/*/*/somewhere',
                '/path/to/*',
                '/path/to/*/',
                '*/to/*/page',
                '/*/to/*/page',
                '/path/*/other/*',
                '/path/*/other/*/',
                'http://www.example.com/',
                'http://www.example.com/*',
                'http://www.example.com/*/to/somewhere',
                'http://www.example.com/path/*/somewhere',
                'http://www.example.com/path/*/lead/*/somewhere',
                'http://www.example.com/path/*/*/somewhere',
                'http://www.example.com/path/to/*',
                'http://www.example.com/path/to/*/',
                'http://www.example.com/*/to/*/page',
                'http://www.example.com/path/*/other/*',
                'http://www.example.com/path/*/other/*/'
            ];
        }

        return self::$validPatternList;
    }

    /**
     * Retrieves list of stores and creates option DOM elements
     *
     * @return string Option DOM elements
     */
    public function getWebSiteoptions()
    {
        $sdstr = '';

        $websites = $this->_storeManager->getWebsites();
        foreach ($websites as $website) {
            $sdstr .= '<option value="'.$website->getId().'">'.$website->getName().'</option>';
        }
        return $sdstr;
    }

    /**
     * Builds the list of valid patterns and creates li DOM elements
     *
     * @return string li DOM elements
     */
    public function getValidPatternsHtml()
    {
        $sdstr = '';
        $patterns = $this->getValidPatternList();

        foreach ($patterns as $pattern) {
            $sdstr .= '<li>' . $pattern . '</li>';
        }
        return $sdstr;
    }

    /**
     * Retrieves iframe url for widget selection.
     *
     * @return string Iframe Url
     */
    public function getIframeUrl()
    {
        return $this->getBaseUrl().'/generic/widgets'
            .'?currentWidgetId=&currentPageId=&transparentBackground=1'
            .'&pltf=magento&pltfv=2&parentDomain='.$this->mainurl();
    }

    /**
     * Retrieves base tawk.to plugin url.
     *
     * @return string base tawk.to plugin url.
     */
    public function getBaseUrl()
    {
        return 'https://plugins.tawk.to';
    }

    /**
     * Generates list of stores available with their hierarchy
     *
     * @return array list of stores available
     */
    public function getHierarchy()
    {
        $websites = $this->_storeManager->getWebsites();

        $h = [];

        $h[] = [
            'id'      => 'global',
            'name'    => 'Global',
            'childs'  => [],
            'current' => $this->getCurrentValuesFor('global')
        ];

        foreach ($websites as $website) {
            $parsed = [];

            $parsed['id']      = $website->getId();
            $parsed['name']    = $website->getName();
            $parsed['childs']  = $this->parseGroups($website->getGroups());
            $parsed['current'] = $this->getCurrentValuesFor($website->getId());

            $h[] = $parsed;
        }

        return $h;
    }

    /**
     * Retrieves list of widget settings.
     *
     * @return array list of widget settings
     */
    public function getCollection()
    {
        return $this->modelWidgetFactory->create()->getCollection();
    }

    /**
     * Save widget action
     */
    public function getFormAction()
    {
        return $this->getUrl('widget/savewidget', ['_secure' => true]);
    }

    /**
     * Remove widget action
     */
    public function getRemoveUrl()
    {
        return $this->getUrl('widget/removewidget', ['_secure' => true]);
    }

    /**
     * Retrieve store widget action
     */
    public function getStoreWidget()
    {
        return $this->getUrl('widget/storewidget', ['_secure' => true]);
    }

    /**
     * Parses group details and its widget settings.
     *
     * @param object[] $groups list of groups.
     * @return object[] list of groups with parsed details.
     */
    private function parseGroups($groups)
    {
        $return = [];

        foreach ($groups as $group) {
            $parsed = [];

            $parsed['id']      = $group->getWebsiteId().'_'.$group->getId();
            $parsed['name']    = $group->getName();
            $parsed['childs']  = $this->parseStores($group->getStores());
            $parsed['current'] = $this->getCurrentValuesFor($parsed['id']);

            $return[] = $parsed;
        }

        return $return;
    }

    /**
     * Parses store details and its widget settings.
     *
     * @param object[] $stores List of stores.
     * @return object[] List of groups with parsed details.
     */
    private function parseStores($stores)
    {
        $return = [];

        foreach ($stores as $store) {
            $parsed = [];

            $parsed['id']      = $store->getWebsiteId().'_'.$store->getGroupId().'_'.$store->getId();
            $parsed['name']    = $store->getName();
            $parsed['childs']  = [];
            $parsed['current'] = $this->getCurrentValuesFor($parsed['id']);

            $return[] = $parsed;
        }

        return $return;
    }

    /**
     * Retrieves property and widget id for provided store/group id
     *
     * @param string $id Store/group id.
     * @return array {
     *   pageId: string,
     *   widgetId: string
     * }
     */
    private function getCurrentValuesFor($id)
    {
        $widgets = $this->getCollection();

        foreach ($widgets as $widget) {
            if ($widget->getForStoreId() === $id) {
                return [
                    'pageId'   => $widget->getPageId(),
                    'widgetId' => $widget->getWidgetId()
                ];
            }
        }

        return [];
    }
}
