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

		return parent::_toHtml();
	}
}