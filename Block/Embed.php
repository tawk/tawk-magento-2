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

class Embed extends Template
{
	const TAWK_EMBED_URL = 'https://embed.tawk.to';
	protected $modelWidgetFactory;
	protected $logger;
	protected $model;
	protected $storeManager;

	public function __construct(WidgetFactory $modelFactory, Template\Context $context, array $data = [])
	{
		parent::__construct($context, $data);
		$this->modelWidgetFactory = $modelFactory;
		$this->storeManager       = $context->getStoreManager();
		$this->logger             = $context->getLogger();
		$this->model              = $this->getWidgetModel();
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

		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

		//order in which we select widget
		$ids = array($websiteId.'_'.$groupId.'_'.$storeId, $websiteId.'_'.$groupId, $websiteId, 'global');

		foreach ($ids as $id) {
			$tmpModel = $objectManager->get('Tawk\Widget\Model\Widget')->loadByForStoreId($id);

			if($tmpModel->hasId()) {
				return $tmpModel;
			}
		}

		return null;
	}

	protected function _toHtml()
	{
		if(is_null($this->model)) {
			return '';
		}

		$alwaysdisplay = $this->model->getAlwaysDisplay();
		$donotdisplay = $this->model->getDoNotDisplay();
		$display = true;

		if($alwaysdisplay == 1){
			$display = true;
			
			$excluded_url_list = $this->model->getExcludeUrl();

			if(strlen( $excluded_url_list ) > 0 )
			{
				$current_url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				$current_url = urldecode($current_url);

				$ssl      = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' );
			    $sp       = strtolower( $_SERVER['SERVER_PROTOCOL'] );
			    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );

			    $current_url = $protocol.'://'.$current_url;
			    $current_url = strtolower($current_url);

			    #$exclude_url = trim( strtolower( $this->model->getExcludeUrl() ) );
			    $current_url = trim( strtolower( $current_url ) );
				
				
				$excluded_url_list = preg_split("/,/", $excluded_url_list);

				foreach($excluded_url_list as $exclude_url)
				{
			    	$exclude_url = strtolower(urldecode(trim($exclude_url)));
			    	if (strpos($current_url, $exclude_url) !== false) 
					{
						$display = false;
					}
				}
			}
		}else{
			$display = false;
		}


		if($donotdisplay == 1){
			$display = false;

			$included_url_list = $this->model->getIncludeUrl();
			if(strlen( $included_url_list ) > 0 )
			{
				$current_url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
				$current_url = urldecode($current_url);

				$ssl      = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' );
			    $sp       = strtolower( $_SERVER['SERVER_PROTOCOL'] );
			    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );

			    $current_url = $protocol.'://'.$current_url;
			    $current_url = strtolower($current_url);

			    $current_url = trim( strtolower( $current_url ) );

				$included_url_list = preg_split("/,/", $included_url_list);
				foreach($included_url_list as $include_url)
				{
			    	$exclude_url = strtolower(urldecode(trim($include_url)));
			    	if (strpos($current_url, $include_url) !== false) 
					{
						$display = true;
					}
				}
			}
		}
		
		if($display == true){		
			return parent::_toHtml();
		}else{
			return '';
		}
	}
}