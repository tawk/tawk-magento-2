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
    const BASE_URL = 'https://plugins.tawk.to';
    protected $logger; 
    protected $modelWidgetFactory;

    public function __construct(Template\Context $context, WidgetFactory $modelFactory, array $data = []) 
    {
        parent::__construct($context, $data);
        $this->logger  = $context->getLogger();
        $this->modelWidgetFactory = $modelFactory;
    }

    function mainurl(){
        if(isset($_SERVER['HTTPS'])){
            $protocol = ($_SERVER['HTTPS'] && $_SERVER['HTTPS'] != "off") ? "https" : "http";
        }
        else{
            $protocol = 'http';
        }
        return $protocol . "://" . $_SERVER['HTTP_HOST'];
    }
    
    public function getWebSiteoptions(){
        $sdstr = '';

        $websites = $this->_storeManager->getWebsites();
        #$sdstr .= '<option value="0">Select Store</option>';
        foreach ($websites as $website) {
            $sdstr .= '<option value="'.$website->getId().'">'.$website->getName().'</option>';
        }
        return $sdstr;
    }

    public function _prepareLayout()
    {
        return parent::_prepareLayout();
    }

    public function getIframeUrl()
    {
        /*
        return $this->getBaseUrl()
            .'/generic/widgets'
            .'?parentDomain='.$this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB)
            .'&selectType=singleIdSelect'
            .'&selectText=Store';
        */
        
        return $this->getBaseUrl().'/generic/widgets'
                .'?currentWidgetId=&currentPageId=&transparentBackground=1'
                .'&parentDomain='.$this->mainurl();

    }

    public function getBaseUrl() {
        return self::BASE_URL;
    }

    public function getHierarchy() {
        $websites = $this->_storeManager->getWebsites();

        $h = array();

        $h[] = array(
            'id'      => 'global',
            'name'    => 'Global',
            'childs'  => array(),
            'current' => $this->getCurrentValuesFor('global')
        );

        foreach ($websites as $website) {
            $parsed = array();

            $parsed['id']      = $website->getId();
            $parsed['name']    = $website->getName();
            $parsed['childs']  = $this->parseGroups($website->getGroups());
            $parsed['current'] = $this->getCurrentValuesFor($website->getId());

            $h[] = $parsed;
        }

        return $h;
    }

    public function getCollection(){
        return $this->modelWidgetFactory->create()->getCollection();
    }

    public function getFormAction() {
        return $this->getUrl('widget/savewidget', ['_secure' => true]);
    }

    public function getRemoveUrl() {
        return $this->getUrl('widget/removewidget', ['_secure' => true]);
    }

    public function getStoreWidget(){
        return $this->getUrl('widget/storewidget', ['_secure' => true]);
    }

    private function parseGroups($groups) {
        $return = array();

        foreach ($groups as $group) {
            $parsed = array();

            $parsed['id']      = $group->getWebsiteId().'_'.$group->getId();
            $parsed['name']    = $group->getName();
            $parsed['childs']  = $this->parseStores($group->getStores());
            $parsed['current'] = $this->getCurrentValuesFor($parsed['id']);

            $return[] = $parsed;
        }

        return $return;
    }

    private function parseStores($stores) {
        $return = array();

        foreach ($stores as $store) {
            $parsed = array();

            $parsed['id']      = $store->getWebsiteId().'_'.$store->getGroupId().'_'.$store->getId();
            $parsed['name']    = $store->getName();
            $parsed['childs']  = array();
            $parsed['current'] = $this->getCurrentValuesFor($parsed['id']);

            $return[] = $parsed;
        }

        return $return;
    }

    private function getCurrentValuesFor($id) {
        $widgets = $this->getCollection();

        foreach ($widgets as $widget) {
            if($widget->getForStoreId() === $id) {
                return array(
                    'pageId'   => $widget->getPageId(),
                    'widgetId' => $widget->getWidgetId()
                );
            }
        }

        return array();
    }
}