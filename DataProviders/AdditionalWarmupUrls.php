<?php
namespace MageSuite\WarmupAdditionalUrls\DataProviders;

class AdditionalWarmupUrls implements \MageSuite\PageCacheWarmer\DataProviders\AdditionalWarmupUrlsInterface
{
    /**
     * @var \Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeInterface;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        \Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\CollectionFactory $productCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->scopeInterface = $scopeInterface;
        $this->storeManager = $storeManager;
    }

    public function getAdditionalUrls()
    {
        return $this->getProductListWarmupUrls();
    }

    public function getProductListWarmupUrls()
    {
        $defaultStoreView = $this->storeManager->getDefaultStoreView();

        $this->storeManager->setCurrentStore($defaultStoreView->getId());
        $productCollection = $this->productCollectionFactory->create();

        $productCollection->addAttributeToSelect('*');
        $productCollection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

        $showOutOfStock = $this->scopeInterface->getValue(
            'cataloginventory/options/show_out_of_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$showOutOfStock) {
            $productCollection->addIsInStockFilter();
        }

        $productCollection->setPageSize(\MageSuite\Frontend\Block\Product\ProductList\Toolbar::LIMIT);

        $lastPage = $productCollection->getLastPageNumber();
        $urls = [];
        for ($i = 1; $i <= $lastPage; $i++) {
            $urls[] = 'frontend/cache/warmup/?p=' . $i;
        }

        return $urls;
    }
}