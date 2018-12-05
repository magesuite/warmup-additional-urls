<?php
namespace MageSuite\WarmupAdditionalUrls\DataProviders;



class AdditionalWarmupUrls implements \MageSuite\PageCacheWarmer\DataProviders\AdditionalWarmupUrlsInterface
{
    /**
     * @var \Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\Collection
     */
    private $productCollection;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeInterface;


    public function __construct(
        \Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\Collection $productCollection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeInterface
    )
    {
        $this->productCollection = $productCollection;
        $this->scopeInterface = $scopeInterface;
    }

    public function getAdditionalUrls()
    {
        $result = [];
        return array_merge($result, $this->getProductListWarmupUrls());
    }

    public function getProductListWarmupUrls()
    {
        $productCollection = $this->productCollection;

        $productCollection->addAttributeToSelect('*');
        $productCollection->addAttributeToFilter('status',\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

        $showOutOfStock = $this->scopeInterface->getValue(
            'cataloginventory/options/show_out_of_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!$showOutOfStock) {
            $productCollection->addIsInStockFilter();
        }

        $productCollection->setPageSize(1000);

        $lastPage = $productCollection->getLastPageNumber();
        $urls = [];
        for ($i = 1; $i <= $lastPage; $i++) {
            $urls[] = 'frontend/cache/warmup/?p=' . $i;
        }

        return $urls;
    }
}