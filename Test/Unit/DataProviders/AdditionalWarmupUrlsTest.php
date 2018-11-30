<?php
namespace MageSuite\WarmupAdditionalUrls\Test\Unit\DataProviders;

class AdditionalWarmupUrlsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productCollection;

    /**
     * @var \MageSuite\WarmupAdditionalUrls\DataProviders\AdditionalWarmupUrls
     */
    protected $dataProvider;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->productCollection = $this->getMockBuilder(\Magento\Catalog\Model\ResourceModel\Product\Collection::class)->disableOriginalConstructor()->getMock();
        $this->dataProvider = $this->objectManager->create(\MageSuite\WarmupAdditionalUrls\DataProviders\AdditionalWarmupUrls::class, ['productCollection' => $this->productCollection]);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     */
    public function testItReturnsCorrectProductListWarmupUrls()
    {
        $this->productCollection->method('getLastPageNumber')->willReturn(12);
        $urls = $this->dataProvider->getProductListWarmupUrls();

        $this->assertEquals(12, count($urls));
        $this->assertEquals('frontend/cache/warmup/?p=12', end($urls));
    }
}