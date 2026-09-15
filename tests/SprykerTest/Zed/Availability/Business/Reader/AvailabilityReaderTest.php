<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Availability\Business\Reader;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\DecimalObject\Decimal;
use Spryker\Zed\Availability\Business\Reader\AvailabilityReader;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStoreFacadeInterface;
use Spryker\Zed\Availability\Persistence\AvailabilityRepositoryInterface;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group Availability
 * @group Business
 * @group Reader
 * @group AvailabilityReaderTest
 * Add your own group annotations below this line
 */
class AvailabilityReaderTest extends Unit
{
    /**
     * @var int
     */
    protected const ID_PRODUCT_CONCRETE = 1;

    /**
     * @var int
     */
    protected const ID_STORE = 1;

    public function testFilterAvailableProductsRequestsCurrentStoreWithFallbackToDefaultWhenProductHasNoStoreRelations(): void
    {
        $productConcreteTransfer = (new ProductConcreteTransfer())
            ->setIdProductConcrete(static::ID_PRODUCT_CONCRETE);

        $storeTransfer = (new StoreTransfer())->setIdStore(static::ID_STORE);

        $storeFacadeMock = $this->createAvailabilityToStoreFacadeMock();
        $storeFacadeMock->expects($this->once())
            ->method('getCurrentStore')
            ->with(true)
            ->willReturn($storeTransfer);

        $availabilityRepositoryMock = $this->createAvailabilityRepositoryMock();
        $availabilityRepositoryMock->method('getMappedProductConcreteAvailabilitiesByProductConcreteIds')
            ->willReturn([
                static::ID_PRODUCT_CONCRETE => (new ProductConcreteAvailabilityTransfer())
                    ->setAvailability(new Decimal(1))
                    ->setIsNeverOutOfStock(false),
            ]);

        $availabilityReader = new AvailabilityReader($availabilityRepositoryMock, $storeFacadeMock);

        $filteredProductConcreteTransfers = $availabilityReader->filterAvailableProducts([$productConcreteTransfer]);

        $this->assertCount(1, $filteredProductConcreteTransfers);
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStoreFacadeInterface
     */
    protected function createAvailabilityToStoreFacadeMock(): AvailabilityToStoreFacadeInterface
    {
        return $this->getMockBuilder(AvailabilityToStoreFacadeInterface::class)
            ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\Availability\Persistence\AvailabilityRepositoryInterface
     */
    protected function createAvailabilityRepositoryMock(): AvailabilityRepositoryInterface
    {
        return $this->getMockBuilder(AvailabilityRepositoryInterface::class)
            ->getMock();
    }
}
