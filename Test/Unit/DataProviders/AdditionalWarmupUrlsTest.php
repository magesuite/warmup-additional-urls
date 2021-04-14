<?php
namespace MageSuite\WarmupAdditionalUrls\Test\Unit\DataProviders;

class AdditionalWarmupUrlsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productCollectionFactory;

    /**
     * @var \Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productCollection;
    /**
     * @var \MageSuite\WarmupAdditionalUrls\DataProviders\AdditionalWarmupUrls
     */
    protected $dataProvider;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->productCollectionFactory = $this->getMockBuilder(\Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\CollectionFactory::class)->disableOriginalConstructor()->getMock();
        $this->productCollection = $this->getMockBuilder(\Smile\ElasticsuiteCatalog\Model\ResourceModel\Product\Fulltext\Collection::class)->disableOriginalConstructor()->getMock();

        $this->dataProvider = $this->objectManager->create(\MageSuite\WarmupAdditionalUrls\DataProviders\AdditionalWarmupUrls::class, ['productCollectionFactory' => $this->productCollectionFactory]);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testItReturnsCorrectProductListWarmupUrls()
    {
        $this->productCollection->method('getLastPageNumber')->willReturn(12);
        $this->productCollectionFactory->method('create')->willReturn($this->productCollection);
        $urls = $this->dataProvider->getProductListWarmupUrls();

        $this->assertEquals(12, count($urls));
        $this->assertEquals('frontend/cache/warmup/?p=12', end($urls));
    }
}
