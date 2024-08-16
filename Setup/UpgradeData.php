<?php

namespace Tawk\Widget\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Store\Model\StoreManagerInterface;

use Tawk\Helpers\PathHelper;
use Tawk\Widget\Model\WidgetFactory;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * Tawk.to Widget Model instance
     *
     * @var WidgetFactory $_modelWidgetFactory
     */
    protected $_modelWidgetFactory;

    /**
     * Store Manager instance
     *
     * @var StoreManagerInterface $_modelStoreManager
     */
    protected $_modelStoreManager;

    /**
     * Constructor
     *
     * @param WidgetFactory $modelWidgetFactory Tawk.to Widget Model instance
     * @param StoreManagerInterface $modelStoreManager Store Manager instance
     */
    public function __construct(
        WidgetFactory $modelWidgetFactory,
        StoreManagerInterface $modelStoreManager
    ) {
        $this->_modelWidgetFactory = $modelWidgetFactory;
        $this->_modelStoreManager = $modelStoreManager;
    }

    /**
     * Upgrade runner
     *
     * @param ModuleDataSetupInterface $setup Module Data Setup instance
     * @param ModuleContextInterface $context Module Context Setup instance
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        $this->versionUpdate160($setup, $context);
        $setup->endSetup();
    }

    /**
     * Upgrade script for version 1.6.0
     *
     * Add new records with wildcards that are derived from the existing patterns.
     *
     * @param [type] $setup
     * @param [type] $context
     * @return void
     */
    private function versionUpdate160($setup, $context)
    {
        if (version_compare($context->getVersion(), '1.6.0', '<')) {
            // get all stores and groups
            $collection = $this->_modelWidgetFactory->create()->getCollection();

            foreach ($collection as $item) {
                $storeId = $item->getForStoreId();
                $storeHost = $this->getStoreHost($storeId);
                $excludePatternList = $this->addWildcardToPatternList($item->getExcludeUrl(), $storeHost);
                $includePatternList = $this->addWildcardToPatternList($item->getIncludeUrl(), $storeHost);

                $item->setExcludeUrl($excludePatternList);
                $item->setIncludeUrl($includePatternList);
                $item->save();
            }
        }
    }

    /**
     * Retrieves store url host
     *
     * @param int $storeId Store Id
     * @return string Store Url Host
     */
    private function getStoreHost($storeId)
    {
        $storeHost = '';

        $storeUrl = $this->_modelStoreManager->getStore($storeId)->getBaseUrl();
        //phpcs:ignore Magento2.Functions.DiscouragedFunction.Discouraged
        $parsedUrl = parse_url($storeUrl);

        if (!empty($parsedUrl['host'])) {
            $storeHost = $parsedUrl['host'];
        }

        if (!empty($parsedUrl['port'])) {
            $storeHost .= ':' . $parsedUrl['port'];
        }

        return $storeHost;
    }

    /**
     * Processes the pattern list and adds a wildcard at the end of the pattern.
     *
     * @param string $patternList Pattern list separated with comma.
     * @param string $storeHost Store Host.
     * @return string Pattern list with wildcards.
     */
    private function addWildcardToPatternList($patternList, $storeHost)
    {
        if (empty($patternList)) {
            return '';
        }
        $splittedPatternList = preg_split("/,/", (string)$patternList);
        $wildcard = PathHelper::get_wildcard();

        $newPatternList = [];
        $addedPatterns = [];

        foreach ($splittedPatternList as $pattern) {
            if (empty($pattern)) {
                continue;
            }

            $pattern = ltrim($pattern, PHP_EOL);
            $pattern = trim($pattern);

            if (strpos($pattern, 'http://') !== 0 &&
                strpos($pattern, 'https://') !== 0 &&
                strpos($pattern, '/') !== 0
            ) {
                // Check if the first part of the string is a host.
                // If not, add a leading / so that the pattern
                // matcher treats is as a path.
                $firstPatternChunk = explode('/', $pattern)[0];
                if ($firstPatternChunk !== $storeHost) {
                    $pattern = '/' . $pattern;
                }
            }

            $newPatternList[] = $pattern;
            $newPattern = $pattern . '/' . $wildcard;
            if (in_array($newPattern, $splittedPatternList, true)) {
                continue;
            }

            if (true === isset($addedPatterns[$newPattern])) {
                continue;
            }

            $newPatternList[]             = $newPattern;
            $addedPatterns[$newPattern] = true;
        }

        // EOL for display purposes
        return join(',' . PHP_EOL, $newPatternList);
    }
}
