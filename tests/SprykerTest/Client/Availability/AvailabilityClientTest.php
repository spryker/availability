<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Client\Availability;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\StorageAvailabilityTransfer;
use Spryker\Client\Availability\AvailabilityClient;
use Spryker\Client\Availability\AvailabilityClientInterface;
use Spryker\Client\Availability\AvailabilityDependencyProvider;
use Spryker\Client\Availability\Dependency\Client\AvailabilityToStorageInterface;
use Spryker\Client\Availability\Exception\ProductAvailabilityNotFoundException;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Client
 * @group Availability
 * @group AvailabilityClientTest
 * Add your own group annotations below this line
 */
class AvailabilityClientTest extends Unit
{
    /**
     * @var int
     */
    public const ID_PRODUCT_ABSTRACT = 5;

    /**
     * @var \SprykerTest\Client\Availability\AvailabilityClientTester
     */
    protected $tester;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->tester->addDependencies();
    }

    /**
     * @return void
     */
    public function testFindProductAvailabilityByIdProductAbstractReturnsProductAvailabilityTransferObject(): void
    {
        // Arrange
        $productAvailability = [];
        $this->setStorageReturn($productAvailability);

        // Act
        $actualProductAvailability = $this->createAvailabilityClient()->findProductAvailabilityByIdProductAbstract(static::ID_PRODUCT_ABSTRACT);

        // Assert
        $this->assertSame(StorageAvailabilityTransfer::class, get_class($actualProductAvailability));
    }

    /**
     * @return void
     */
    public function testFindProductAvailabilityByIdProductAbstractReturnsNullWhenProductAvailabilityWasNotFoundInStorage(): void
    {
        // Arrange
        $productAvailability = null;
        $this->setStorageReturn($productAvailability);

        // Act
        $actualResult = $this->createAvailabilityClient()->findProductAvailabilityByIdProductAbstract(static::ID_PRODUCT_ABSTRACT);

        // Assert
        $this->assertNull($actualResult);
    }

    /**
     * @return void
     */
    public function testGetProductAvailabilityByIdProductAbstractReturnsProductAvailabilityTransferObject(): void
    {
        // Arrange
        $productAvailability = [];
        $this->setStorageReturn($productAvailability);

        // Act
        $actualProductAvailability = $this->createAvailabilityClient()->getProductAvailabilityByIdProductAbstract(static::ID_PRODUCT_ABSTRACT);

        // Assert
        $this->assertSame(StorageAvailabilityTransfer::class, get_class($actualProductAvailability));
    }

    /**
     * @return void
     */
    public function testGetProductAvailabilityByIdProductAbstractThrowsExceptionWhenProductAvailabilityWasNotFoundInStorage(): void
    {
        // Arrange
        $productAvailability = null;
        $this->setStorageReturn($productAvailability);

        // Assert
        $this->expectException(ProductAvailabilityNotFoundException::class);

        // Act
        $this->createAvailabilityClient()->getProductAvailabilityByIdProductAbstract(static::ID_PRODUCT_ABSTRACT);
    }

    /**
     * @param array|null $returnedProductAvailability
     *
     * @return void
     */
    protected function setStorageReturn(?array $returnedProductAvailability): void
    {
        $availabilityToStorageBridge = $this->getMockBuilder(AvailabilityToStorageInterface::class)->getMock();
        $availabilityToStorageBridge->method('get')->willReturn($returnedProductAvailability);
        $this->tester->setDependency(AvailabilityDependencyProvider::KV_STORAGE, $availabilityToStorageBridge);
    }

    /**
     * @return \Spryker\Client\Availability\AvailabilityClientInterface
     */
    protected function createAvailabilityClient(): AvailabilityClientInterface
    {
        return new AvailabilityClient();
    }
}
