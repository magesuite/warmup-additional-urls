<?php
namespace MageSuite\WarmupAdditionalUrls\DataProviders;



class AdditionalWarmupUrls implements \MageSuite\PageCacheWarmer\DataProviders\AdditionalWarmupUrlsInterface
{
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private $productCollection;


    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
    )
    {
        $this->productCollection = $productCollection;
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

        $lastPage = $productCollection->getLastPageNumber();
        $urls = [];
        for ($i = 1; $i <= $lastPage; $i++) {
            $urls[] = 'frontend/cache/warmup/?p=' . $i;
        }

        return $urls;
    }
}