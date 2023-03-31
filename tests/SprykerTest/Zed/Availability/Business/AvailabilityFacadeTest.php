<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Availability\Business;

use ArrayObject;
use Codeception\Test\Unit;
use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ProductAvailabilityCriteriaTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityRequestTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SellableItemsResponseTransfer;
use Generated\Shared\Transfer\StockProductTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\WishlistItemTransfer;
use Orm\Zed\Availability\Persistence\SpyAvailability;
use Orm\Zed\Availability\Persistence\SpyAvailabilityAbstractQuery;
use Orm\Zed\Availability\Persistence\SpyAvailabilityQuery;
use Orm\Zed\Product\Persistence\SpyProductAbstractQuery;
use Orm\Zed\Product\Persistence\SpyProductQuery;
use Orm\Zed\Stock\Persistence\SpyStockProduct;
use Orm\Zed\Stock\Persistence\SpyStockProductQuery;
use Orm\Zed\Stock\Persistence\SpyStockQuery;
use Spryker\DecimalObject\Decimal;
use Spryker\Zed\Availability\AvailabilityDependencyProvider;
use Spryker\Zed\Availability\Business\AvailabilityBusinessFactory;
use Spryker\Zed\Availability\Business\AvailabilityFacade;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStockFacadeInterface;
use Spryker\Zed\AvailabilityExtension\Dependency\Plugin\AvailabilityStrategyPluginInterface;
use Spryker\Zed\AvailabilityExtension\Dependency\Plugin\BatchAvailabilityStrategyPluginInterface;
use Spryker\Zed\Kernel\Container;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Availability
 * @group Business
 * @group Facade
 * @group AvailabilityFacadeTest
 * Add your own group annotations below this line
 */
class AvailabilityFacadeTest extends Unit
{
    /**
     * @var string
     */
    protected const ABSTRACT_SKU = '123_availability_test';

    /**
     * @var string
     */
    protected const CONCRETE_SKU = '123_availability_test-concrete';

    /**
     * @var int
     */
    protected const ID_STORE = 1;

    /**
     * @var string
     */
    protected const STORE_NAME_DE = 'DE';

    /**
     * @var string
     */
    protected const STORE_NAME_AT = 'AT';

    /**
     * @var string
     */
    protected const COL_QUANTITY = 'quantity';

    /**
     * @var \SprykerTest\Zed\Availability\AvailabilityBusinessTester
     */
    protected $tester;

    /**
     * @return void
     */
    public function testIsProductSellableWhenNeverOutOfStockShouldReturnSuccess(): void
    {
        // Arrange
        $this->tester->haveProduct([ProductConcreteTransfer::SKU => static::CONCRETE_SKU]);
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            ['is_never_out_of_stock' => true],
            $storeTransfer,
        );

        // Act
        $isProductSellable = $this->getAvailabilityFacade()
            ->isProductSellableForStore(static::CONCRETE_SKU, new Decimal(1), $storeTransfer);

        // Assert
        $this->assertTrue($isProductSellable);
    }

    /**
     * @return void
     */
    public function testIsProductSellableWhenStockIsEmptyShouldReturnFailure(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            ['quantity' => 0],
            $storeTransfer,
        );

        // Act
        $isProductSellable = $this->getAvailabilityFacade()
            ->isProductSellableForStore(static::CONCRETE_SKU, new Decimal(1), $storeTransfer);

        // Assert
        $this->assertFalse($isProductSellable);
    }

    /**
     * @return void
     */
    public function testIsProductSellableWhenStockFulfilledShouldReturnSuccess(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            ['quantity' => 5],
            $storeTransfer,
        );

        // Act
        $isProductSellable = $this->getAvailabilityFacade()
            ->isProductSellableForStore(static::CONCRETE_SKU, new Decimal(1), $storeTransfer);

        // Assert
        $this->assertTrue($isProductSellable);
    }

    /**
     * @dataProvider provideTestDecimalQuantity
     *
     * @param \Spryker\DecimalObject\Decimal $quantity
     *
     * @return void
     */
    public function testCalculateStockForProductShouldReturnPersistedStock(Decimal $quantity): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            ['quantity' => $quantity->toString()],
            $storeTransfer,
        );

        // Act
        $calculatedQuantity = $this->getAvailabilityFacade()
            ->calculateAvailabilityForProductWithStore(static::CONCRETE_SKU, $storeTransfer);

        // Assert
        $this->assertTrue($calculatedQuantity->equals($quantity));
    }

    /**
     * @return array
     */
    public function provideTestDecimalQuantity(): array
    {
        return [
            'int stock' => [new Decimal(5)],
            'float stock' => [new Decimal(5.5)],
        ];
    }

    /**
     * @return void
     */
    public function testCheckAvailabilityPreConditionShouldNotWriteErrorsWhenAvailabilityIsSatisfied(): void
    {
        // Arrange
        $checkoutResponseTransfer = new CheckoutResponseTransfer();
        $quoteTransfer = $this->createQuoteTransfer();
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            [static::COL_QUANTITY => 5],
            $quoteTransfer->getStore(),
        );

        // Act
        $this->tester->getFacade()
            ->checkoutAvailabilityPreCondition($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertEmpty($checkoutResponseTransfer->getErrors());
    }

    /**
     * @return void
     */
    public function testCheckAvailabilityPreConditionShouldWriteErrorWhenAvailabilityIsNotSatisfied(): void
    {
        // Arrange
        $checkoutResponseTransfer = new CheckoutResponseTransfer();
        $quoteTransfer = $this->createQuoteTransfer();
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            [static::COL_QUANTITY => 0],
            $quoteTransfer->getStore(),
        );

        // Act
        $this->tester->getFacade()
            ->checkoutAvailabilityPreCondition($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertCount(1, $checkoutResponseTransfer->getErrors());
    }

    /**
     * @return void
     */
    public function testCheckAvailabilityPreConditionExecutesAvailabilityStrategyPluginStack(): void
    {
        // Arrange
        $quoteTransfer = $this->createQuoteTransfer();
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            [static::COL_QUANTITY => 1],
            $quoteTransfer->getStore(),
        );

        $availabilityStrategyPluginMock = $this->getMockBuilder(AvailabilityStrategyPluginInterface::class)
            ->getMock();

        $availabilityStrategyPluginMock
            ->expects($this->once())
            ->method('isApplicable')
            ->willReturn(true);

        $availabilityStrategyPluginMock
            ->expects($this->once())
            ->method('findProductConcreteAvailabilityForStore');

        $this->tester->setDependency(AvailabilityDependencyProvider::PLUGINS_AVAILABILITY_STRATEGY, [
            $availabilityStrategyPluginMock,
        ]);

        // Act
        $this->tester->getFacade()
            ->checkoutAvailabilityPreCondition($quoteTransfer, new CheckoutResponseTransfer());
    }

    /**
     * @return void
     */
    public function testCheckAvailabilityPreConditionWillExecuteBatchAvailabilityStrategyPlugins(): void
    {
        // Arrange
        $checkoutResponseTransfer = new CheckoutResponseTransfer();
        $quoteTransfer = $this->createQuoteTransfer();
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            [static::COL_QUANTITY => 0],
            $quoteTransfer->getStore(),
        );

        $batchAvailabilityStrategyPluginMock = $this->getMockBuilder(BatchAvailabilityStrategyPluginInterface::class)
            ->getMock();

        $batchAvailabilityStrategyPluginMock
            ->expects($this->once())
            ->method('findItemsAvailabilityForStore')
            ->willReturn((new SellableItemsResponseTransfer())->setSellableItemResponses(new ArrayObject()));

        $this->tester->setDependency(AvailabilityDependencyProvider::PLUGINS_BATCH_AVAILABILITY_STRATEGY, [
            $batchAvailabilityStrategyPluginMock,
        ]);

        // Act
        $this->tester->getFacade()
            ->checkoutAvailabilityPreCondition($quoteTransfer, $checkoutResponseTransfer);

        // Assert
        $this->assertEmpty($checkoutResponseTransfer->getErrors());
    }

    /**
     * @return void
     */
    public function testUpdateAvailabilityShouldStoreNewQuantity(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $stockProductEntity = $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            ['quantity' => 5],
            $storeTransfer,
        );

        $stockProductEntity->setQuantity(50);
        $stockProductEntity->save();

        // Act
        $this->getAvailabilityFacade()->updateAvailability(static::CONCRETE_SKU);

        // Assert
        $availabilityEntity = SpyAvailabilityQuery::create()->findOneBySku(static::CONCRETE_SKU);
        $this->assertNotNull($availabilityEntity);
        $this->assertSame(50, (new Decimal($availabilityEntity->getQuantity()))->toInt());

        $availabilityAbstractEntity = SpyAvailabilityAbstractQuery::create()->findOneByAbstractSku(static::ABSTRACT_SKU);
        $this->assertNotNull($availabilityAbstractEntity);
        $this->assertSame(50, (new Decimal($availabilityAbstractEntity->getQuantity()))->toInt());
    }

    /**
     * @return void
     */
    public function testUpdateAvailabilityWhenItsEmptyShouldStoreNewQuantity(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            ['quantity' => 50],
            $storeTransfer,
        );
        $this->createProductAvailability(static::ABSTRACT_SKU, static::CONCRETE_SKU, new Decimal(0), $storeTransfer);

        // Act
        $this->getAvailabilityFacade()->updateAvailability(static::CONCRETE_SKU);

        // Assert
        $availabilityEntity = SpyAvailabilityQuery::create()->findOneBySku(static::CONCRETE_SKU);
        $this->assertSame(50, (new Decimal($availabilityEntity->getQuantity()))->toInt());
    }

    /**
     * @return void
     */
    public function testUpdateAvailabilityWhenSetToEmptyShouldStoreEmptyQuantity(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $productTransfer = $this->tester->haveProduct();
        $this->tester->haveProductInStockForStore($storeTransfer, [
            StockProductTransfer::SKU => $productTransfer->getSku(),
            StockProductTransfer::QUANTITY => 0,
            StockProductTransfer::IS_NEVER_OUT_OF_STOCK => false,
        ]);

        // Act
        $this->getAvailabilityFacade()->updateAvailability($productTransfer->getSku());

        // Assert
        $availabilityEntity = SpyAvailabilityQuery::create()->findOneBySku($productTransfer->getSku());
        $this->assertNotNull($availabilityEntity);
        $this->assertSame(0, (new Decimal($availabilityEntity->getQuantity()))->toInt());
        $availabilityAbstractEntity = SpyAvailabilityAbstractQuery::create()->findOneByAbstractSku($productTransfer->getAbstractSku());
        $this->assertNotNull($availabilityAbstractEntity);
        $this->assertSame(0, (new Decimal($availabilityAbstractEntity->getQuantity()))->toInt());
    }

    /**
     * @return void
     */
    public function testFindProductAbstractAvailabilityForStoreWithCachedAvailability(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $productTransfer = $this->tester->haveProduct([], ['sku' => static::ABSTRACT_SKU]);
        $this->tester->haveAvailabilityAbstract($productTransfer, new Decimal(2), $storeTransfer->getIdStore());

        // Act
        $productAbstractAvailabilityTransfer = $this->getAvailabilityFacade()
            ->findOrCreateProductAbstractAvailabilityBySkuForStore(
                $productTransfer->getAbstractSku(),
                $storeTransfer,
            );

        // Assert
        $this->assertNotNull($productAbstractAvailabilityTransfer);
        $this->assertSame(2, $productAbstractAvailabilityTransfer->getAvailability()->trim()->toInt());
    }

    /**
     * @return void
     */
    public function testFindProductAbstractAvailabilityForStoreWithInvalidSku(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);

        // Act
        $productAbstractAvailabilityTransfer = $this->getAvailabilityFacade()
            ->findOrCreateProductAbstractAvailabilityBySkuForStore(
                'xyz' . rand(100, 1000),
                $storeTransfer,
            );

        // Assert
        $this->assertNull($productAbstractAvailabilityTransfer);
    }

    /**
     * @return void
     */
    public function testFindProductAbstractAvailabilityForStoreWithStockAndNoCachedAvailability(): void
    {
        // Arrange
        $abstractSku = 'testFindProductAbstractAvailabilityForStoreAbstract';
        $concreteSku1 = 'testFindProductAbstractAvailabilityForStore1';
        $concreteSku2 = 'testFindProductAbstractAvailabilityForStore2';
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $productQuantity1 = rand(1, 10);
        $productQuantity2 = rand(1, 10);
        $this->createProductWithStock(
            $abstractSku,
            $concreteSku1,
            ['quantity' => $productQuantity1],
            $storeTransfer,
        );

        $this->createProductWithStock(
            $abstractSku,
            $concreteSku2,
            ['quantity' => $productQuantity2],
            $storeTransfer,
        );

        // Act
        $productAbstractAvailabilityTransfer = $this->getAvailabilityFacade()
            ->findOrCreateProductAbstractAvailabilityBySkuForStore(
                $abstractSku,
                $storeTransfer,
            );

        // Assert
        $this->assertNotNull($productAbstractAvailabilityTransfer);
        $this->assertSame(
            $productAbstractAvailabilityTransfer->getAvailability()->trim()->toInt(),
            ($productQuantity1 + $productQuantity2),
        );
    }

    /**
     * @return void
     */
    public function testFindProductConcreteAvailabilityBySkuForStoreWithCachedAvailability(): void
    {
        // Arrange
        $productQuantity = 6;
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $productTransfer = $this->tester->haveProduct(['sku' => static::CONCRETE_SKU], ['sku' => static::ABSTRACT_SKU]);
        $this->tester->haveAvailabilityConcrete($productTransfer->getSku(), $storeTransfer, new Decimal($productQuantity));

        // Act
        $productConcreteAvailabilityTransfer = $this->getAvailabilityFacade()
            ->findOrCreateProductConcreteAvailabilityBySkuForStore(
                $productTransfer->getSku(),
                $storeTransfer,
            );

        // Assert
        $this->assertNotNull($productConcreteAvailabilityTransfer);
        $this->assertSame($productQuantity, $productConcreteAvailabilityTransfer->getAvailability()->trim()->toInt());
    }

    /**
     * @return void
     */
    public function testFindProductConcreteAvailabilityBySkuForStoreWithStockAndNoCachedAvailability(): void
    {
        // Arrange
        $productQuantity = 13;
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $stockProductEntity = $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            ['quantity' => $productQuantity],
            $storeTransfer,
        );

        // Act
        $productConcreteAvailabilityTransfer = $this->getAvailabilityFacade()
            ->findOrCreateProductConcreteAvailabilityBySkuForStore(
                static::CONCRETE_SKU,
                $storeTransfer,
            );

        // Assert
        $this->assertNotNull($productConcreteAvailabilityTransfer);
        $this->assertSame($productQuantity, $productConcreteAvailabilityTransfer->getAvailability()->trim()->toInt());
    }

    /**
     * @return void
     */
    public function testFindProductConcreteAvailabilityBySkuForStoreWithInvalidSku(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);

        // Act
        $productConcreteAvailabilityTransfer = $this->getAvailabilityFacade()
            ->findOrCreateProductConcreteAvailabilityBySkuForStore(
                'xyz' . rand(100, 1000),
                $storeTransfer,
            );

        // Assert
        $this->assertNull($productConcreteAvailabilityTransfer);
    }

    /**
     * @return void
     */
    public function testSaveProductAvailabilityForStoreShouldStoreAvailability(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            ['quantity' => 0],
            $storeTransfer,
        );

        // Act
        $this->getAvailabilityFacade()
            ->saveProductAvailabilityForStore(static::CONCRETE_SKU, new Decimal(2), $storeTransfer);

        // Assert
        $productConcreteAvailabilityTransfer = $this->getAvailabilityFacade()
            ->findProductConcreteAvailability(
                (new ProductConcreteAvailabilityRequestTransfer())
                    ->setSku(static::CONCRETE_SKU),
            );

        $this->assertTrue($productConcreteAvailabilityTransfer->getAvailability()->equals(2));
    }

    /**
     * @return void
     */
    public function testIsProductConcreteAvailable(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct();
        $productConcreteTransfer2 = $this->tester->haveProduct();
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);

        $this->getAvailabilityFacade()->saveProductAvailabilityForStore(
            $productConcreteTransfer->getSku(),
            new Decimal('1.1'),
            $storeTransfer,
        );

        // Act
        $productAvailable = $this->getAvailabilityFacade()
            ->isProductConcreteAvailable($productConcreteTransfer->getIdProductConcrete());

        $productAvailable2 = $this->getAvailabilityFacade()
            ->isProductConcreteAvailable($productConcreteTransfer2->getIdProductConcrete());

        // Assert
        $this->assertTrue($productAvailable);
        $this->assertFalse($productAvailable2);
    }

    /**
     * @return void
     */
    public function testFilterAvailableProductsWithNeverOutOfStock(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct([ProductConcreteTransfer::SKU => static::CONCRETE_SKU]);
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            ['is_never_out_of_stock' => true],
            $storeTransfer,
        );

        // Act
        $productConcreteTransfers = $this->getAvailabilityFacade()
            ->filterAvailableProducts([$productConcreteTransfer]);

        // Assert
        $this->assertCount(1, $productConcreteTransfers);
    }

    /**
     * @return void
     */
    public function testFilterAvailableProductsWithQuantity(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct([ProductConcreteTransfer::SKU => static::CONCRETE_SKU]);
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            ['quantity' => 2],
            $storeTransfer,
        );

        // Act
        $productConcreteTransfers = $this->getAvailabilityFacade()
            ->filterAvailableProducts([$productConcreteTransfer]);

        // Assert
        $this->assertCount(1, $productConcreteTransfers);
    }

    /**
     * @return void
     */
    public function testFilterAvailableProductsWithZeroQuantity(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct([ProductConcreteTransfer::SKU => static::CONCRETE_SKU]);
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $this->createProductWithStock(
            static::ABSTRACT_SKU,
            static::CONCRETE_SKU,
            ['quantity' => 0],
            $storeTransfer,
        );

        // Act
        $productConcreteTransfers = $this->getAvailabilityFacade()
            ->filterAvailableProducts([$productConcreteTransfer]);

        // Assert
        $this->assertCount(0, $productConcreteTransfers);
    }

    /**
     * @return void
     */
    public function testFilterAvailableProductsWithoutStock(): void
    {
        // Arrange
        $productConcreteTransfer = $this->tester->haveProduct([ProductConcreteTransfer::SKU => static::CONCRETE_SKU]);
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);

        // Act
        $productConcreteTransfers = $this->getAvailabilityFacade()
            ->filterAvailableProducts([$productConcreteTransfer]);

        // Assert
        $this->assertCount(0, $productConcreteTransfers);
    }

    /**
     * @return void
     */
    public function testFilterAvailableProductsWithSeveralItems(): void
    {
        // Arrange
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);

        $firstProductConcreteTransfer = $this->tester->haveProduct();
        $this->createProductWithStock(
            $firstProductConcreteTransfer->getAbstractSku(),
            $firstProductConcreteTransfer->getSku(),
            ['quantity' => 0],
            $storeTransfer,
        );

        $secondProductConcreteTransfer = $this->tester->haveProduct();
        $this->createProductWithStock(
            $firstProductConcreteTransfer->getAbstractSku(),
            $firstProductConcreteTransfer->getSku(),
            ['quantity' => 2],
            $storeTransfer,
        );

        // Act
        $productConcreteTransfers = $this->getAvailabilityFacade()
            ->filterAvailableProducts([$firstProductConcreteTransfer, $secondProductConcreteTransfer]);

        // Assert
        $this->assertCount(1, $productConcreteTransfers);
    }

    /**
     * @return void
     */
    public function testGetProductConcreteAvailabilityCollectionReturnsAllAvailabilitiesWithNoCriteria(): void
    {
        // Arrange
        $this->tester->ensureAvailabilityTableIsEmpty();
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $productTransfer1 = $this->tester->haveProduct(['sku' => static::CONCRETE_SKU]);
        $productTransfer2 = $this->tester->haveProduct(['sku' => static::CONCRETE_SKU . 1]);
        $productTransfer3 = $this->tester->haveProduct(['sku' => static::CONCRETE_SKU . 2]);
        $this->tester->haveAvailabilityConcrete($productTransfer1->getSku(), $storeTransfer, new Decimal(1));
        $this->tester->haveAvailabilityConcrete($productTransfer2->getSku(), $storeTransfer, new Decimal(2));
        $this->tester->haveAvailabilityConcrete($productTransfer3->getSku(), $storeTransfer, new Decimal(3));

        // Act
        $productConcreteAvailabilityCollectionTransfer = $this->getAvailabilityFacade()->getProductConcreteAvailabilityCollection(
            new ProductAvailabilityCriteriaTransfer(),
        );

        // Assert
        $this->assertCount(3, $productConcreteAvailabilityCollectionTransfer->getProductConcreteAvailabilities());
    }

    /**
     * @return void
     */
    public function testGetProductConcreteAvailabilityCollectionReturnsAvailabilitiesFilteredBySkus(): void
    {
        // Arrange
        $this->tester->ensureAvailabilityTableIsEmpty();
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $productTransfer1 = $this->tester->haveProduct(['sku' => static::CONCRETE_SKU]);
        $productTransfer2 = $this->tester->haveProduct(['sku' => static::CONCRETE_SKU . 1]);
        $this->tester->haveAvailabilityConcrete($productTransfer1->getSku(), $storeTransfer, new Decimal(1));
        $this->tester->haveAvailabilityConcrete($productTransfer2->getSku(), $storeTransfer, new Decimal(2));

        // Act
        $productConcreteAvailabilityCollectionTransfer = $this->getAvailabilityFacade()->getProductConcreteAvailabilityCollection(
            (new ProductAvailabilityCriteriaTransfer())->addProductConcreteSku($productTransfer2->getSku()),
        );

        // Assert
        $this->assertCount(1, $productConcreteAvailabilityCollectionTransfer->getProductConcreteAvailabilities());
        $this->assertSame(
            $productConcreteAvailabilityCollectionTransfer->getProductConcreteAvailabilities()[0]->getSku(),
            $productTransfer2->getSku(),
        );
        $this->assertSame(
            $productConcreteAvailabilityCollectionTransfer->getProductConcreteAvailabilities()[0]->getAvailability()->toInt(),
            2,
        );
    }

    /**
     * @return void
     */
    public function testGetProductConcreteAvailabilityCollectionReturnsAvailabilitiesFilteredByStoreIds(): void
    {
        // Arrange
        $this->tester->ensureAvailabilityTableIsEmpty();
        $storeTransfer1 = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE], false);
        $storeTransfer2 = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_AT], false);
        $productTransfer1 = $this->tester->haveProduct(['sku' => static::CONCRETE_SKU]);
        $productTransfer2 = $this->tester->haveProduct(['sku' => static::CONCRETE_SKU . 1]);
        $this->tester->haveAvailabilityConcrete($productTransfer1->getSku(), $storeTransfer1, new Decimal(1));
        $this->tester->haveAvailabilityConcrete($productTransfer2->getSku(), $storeTransfer2, new Decimal(2));

        // Act
        $productConcreteAvailabilityCollectionTransfer = $this->getAvailabilityFacade()->getProductConcreteAvailabilityCollection(
            (new ProductAvailabilityCriteriaTransfer())->addIdStore($storeTransfer1->getIdStore()),
        );

        // Assert
        $this->assertCount(1, $productConcreteAvailabilityCollectionTransfer->getProductConcreteAvailabilities());
        $this->assertSame(
            $productConcreteAvailabilityCollectionTransfer->getProductConcreteAvailabilities()[0]->getSku(),
            $productTransfer1->getSku(),
        );
        $this->assertSame(
            $productConcreteAvailabilityCollectionTransfer->getProductConcreteAvailabilities()[0]->getAvailability()->toInt(),
            1,
        );
    }

    /**
     * @return void
     */
    public function testGetProductConcreteAvailabilityCollectionReturnsNoAvailabilitiesIfNoAvailabilitiesMeetCriteria(): void
    {
        // Arrange
        $this->tester->ensureAvailabilityTableIsEmpty();
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $productTransfer1 = $this->tester->haveProduct(['sku' => static::CONCRETE_SKU]);
        $productTransfer2 = $this->tester->haveProduct(['sku' => static::CONCRETE_SKU . 1]);
        $this->tester->haveAvailabilityConcrete($productTransfer1->getSku(), $storeTransfer, new Decimal(1));
        $this->tester->haveAvailabilityConcrete($productTransfer2->getSku(), $storeTransfer, new Decimal(2));

        // Act
        $productConcreteAvailabilityCollectionTransfer = $this->getAvailabilityFacade()->getProductConcreteAvailabilityCollection(
            (new ProductAvailabilityCriteriaTransfer())
                ->addIdStore($storeTransfer->getIdStore() + 1)
                ->addProductConcreteSku(static::CONCRETE_SKU . 2),
        );

        // Assert
        $this->assertCount(0, $productConcreteAvailabilityCollectionTransfer->getProductConcreteAvailabilities());
    }

    /**
     * @return void
     */
    public function testExpandWishlistItemWithAvailabilitySuccess(): void
    {
        // Arrange
        $this->tester->ensureAvailabilityTableIsEmpty();
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $productTransfer1 = $this->tester->haveProduct(['sku' => static::CONCRETE_SKU]);
        $wishlsitItemTransfer = (new WishlistItemTransfer())->setSku($productTransfer1->getSku());
        $productConcreteAvailabilityTransfer = $this->tester->haveAvailabilityConcrete($productTransfer1->getSku(), $storeTransfer, new Decimal(1));

        // Act
        $wishlsitItemTransfer = $this->tester->getFacade()->expandWishlistItemWithAvailability($wishlsitItemTransfer);

        // Assert
        $this->assertSame($wishlsitItemTransfer->getProductConcreteAvailability()->getSku(), $productConcreteAvailabilityTransfer->getSku());
        $this->assertSame($wishlsitItemTransfer->getProductConcreteAvailability()->getIsNeverOutOfStock(), $productConcreteAvailabilityTransfer->getIsNeverOutOfStock());
    }

    /**
     * @return void
     */
    public function testExpandWishlistItemWithSellableSuccess(): void
    {
        // Arrange
        $this->tester->ensureAvailabilityTableIsEmpty();
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $productTransfer1 = $this->tester->haveProduct(['sku' => static::CONCRETE_SKU]);
        $wishlsitItemTransfer = (new WishlistItemTransfer())->setSku($productTransfer1->getSku());
        $productConcreteAvailabilityTransfer = $this->tester->haveAvailabilityConcrete($productTransfer1->getSku(), $storeTransfer, new Decimal(1));

        // Act
        $wishlsitItemTransfer = $this->tester->getFacade()->expandWishlistItemWithSellable($wishlsitItemTransfer);

        // Assert
        $this->assertTrue($wishlsitItemTransfer->getIsSellable());
    }

    /**
     * @return \Spryker\Zed\Availability\Business\AvailabilityFacade
     */
    protected function getAvailabilityFacade(): AvailabilityFacade
    {
        /** @var \Spryker\Zed\Availability\Business\AvailabilityFacade $availabilityFacade */
        $availabilityFacade = $this->tester->getFacade();

        $container = new Container();
        $container->set(AvailabilityDependencyProvider::FACADE_STOCK, function () {
            return $this->createStockFacadeMock();
        });
        $availabilityBusinessFactory = new AvailabilityBusinessFactory();
        $dependencyProvider = new AvailabilityDependencyProvider();
        $dependencyProvider->provideBusinessLayerDependencies($container);
        $availabilityBusinessFactory->setContainer($container);
        $availabilityFacade->setFactory($availabilityBusinessFactory);

        return $availabilityFacade;
    }

    /**
     * @param string $abstractSku
     * @param string $concreteSku
     * @param array $stockData
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Orm\Zed\Stock\Persistence\SpyStockProduct
     */
    protected function createProductWithStock(
        string $abstractSku,
        string $concreteSku,
        array $stockData,
        StoreTransfer $storeTransfer
    ): SpyStockProduct {
        $productAbstractEntity = (new SpyProductAbstractQuery())
            ->filterBySku($abstractSku)
            ->findOneOrCreate();
        $productAbstractEntity->setAttributes('');
        $productAbstractEntity->save();

        $productEntity = (new SpyProductQuery())
            ->filterBySku($concreteSku)
            ->findOneOrCreate();

        $productEntity->setAttributes('');
        $productEntity->setIsActive(true);
        $productEntity->setFkProductAbstract($productAbstractEntity->getIdProductAbstract());
        $productEntity->save();

        $stockEntity = (new SpyStockQuery())
            ->filterByName('Warehouse1')
            ->findOneOrCreate();

        $stockEntity->save();

        $stockProductEntity = (new SpyStockProductQuery())
            ->filterByFkProduct($productEntity->getIdProduct())
            ->filterByFkStock($stockEntity->getIdStock())
            ->findOneOrCreate();

        $stockProductEntity->fromArray($stockData);
        $stockProductEntity->save();

        $this->getAvailabilityFacade()->updateAvailabilityForStore($concreteSku, $storeTransfer);

        return $stockProductEntity;
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer(): QuoteTransfer
    {
        $storeTransfer = $this->tester->haveStore([StoreTransfer::NAME => static::STORE_NAME_DE]);
        $quoteTransfer = new QuoteTransfer();
        $quoteTransfer->setStore($storeTransfer);
        $itemTransfer = new ItemTransfer();
        $itemTransfer->setSku(static::CONCRETE_SKU);
        $itemTransfer->setQuantity(1);
        $quoteTransfer->addItem($itemTransfer);

        return $quoteTransfer;
    }

    /**
     * @param string $abstractSku
     * @param string $concreteSku
     * @param \Spryker\DecimalObject\Decimal $quantity
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Orm\Zed\Availability\Persistence\SpyAvailability
     */
    protected function createProductAvailability(
        string $abstractSku,
        string $concreteSku,
        Decimal $quantity,
        StoreTransfer $storeTransfer
    ): SpyAvailability {
        $availabilityAbstractEntity = (new SpyAvailabilityAbstractQuery())
            ->filterByAbstractSku($abstractSku)
            ->filterByFkStore($storeTransfer->getIdStore())
            ->findOneOrCreate();

        $availabilityAbstractEntity->setQuantity($quantity)->save();

        $availabilityEntity = (new SpyAvailabilityQuery())
            ->filterByFkAvailabilityAbstract($availabilityAbstractEntity->getIdAvailabilityAbstract())
            ->filterByFkStore($storeTransfer->getIdStore())
            ->filterBySku($concreteSku)
            ->findOneOrCreate();

        $availabilityEntity->setQuantity($quantity)->save();

        return $availabilityEntity;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStockFacadeInterface
     */
    protected function createStockFacadeMock(): AvailabilityToStockFacadeInterface
    {
        $mock = $this->getMockBuilder(AvailabilityToStockFacadeInterface::class)->getMock();
        $mock->method('getStoreToWarehouseMapping')
            ->willReturn([static::STORE_NAME_DE => ['Warehouse1']]);
        $mock->method('getStoresWhereProductStockIsDefined')
            ->willReturn((new StoreTransfer())->setName(static::STORE_NAME_DE));

        return $mock;
    }
}
