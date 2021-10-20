<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Business\Model;

use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityRequestTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Orm\Zed\Availability\Persistence\SpyAvailability;
use Orm\Zed\Product\Persistence\SpyProductAbstract;
use Spryker\DecimalObject\Decimal;
use Spryker\Zed\Availability\Business\Exception\ProductAbstractAvailabilityNotFoundException;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStockFacadeInterface;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStoreFacadeInterface;
use Spryker\Zed\Availability\Persistence\AvailabilityQueryContainer;
use Spryker\Zed\Availability\Persistence\AvailabilityQueryContainerInterface;

/**
 * @deprecated Use ProductAvailabilityReader instead.
 */
class ProductReservationReader implements ProductReservationReaderInterface
{
    /**
     * @var \Spryker\Zed\Availability\Persistence\AvailabilityQueryContainerInterface
     */
    protected $availabilityQueryContainer;

    /**
     * @var \Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStockFacadeInterface
     */
    protected $stockFacade;

    /**
     * @var \Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @param \Spryker\Zed\Availability\Persistence\AvailabilityQueryContainerInterface $availabilityQueryContainer
     * @param \Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStockFacadeInterface $stockFacade
     * @param \Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStoreFacadeInterface $storeFacade
     */
    public function __construct(
        AvailabilityQueryContainerInterface $availabilityQueryContainer,
        AvailabilityToStockFacadeInterface $stockFacade,
        AvailabilityToStoreFacadeInterface $storeFacade
    ) {
        $this->availabilityQueryContainer = $availabilityQueryContainer;
        $this->stockFacade = $stockFacade;
        $this->storeFacade = $storeFacade;
    }

    /**
     * @param int $idProductAbstract
     * @param int $idLocale
     *
     * @throws \Spryker\Zed\Availability\Business\Exception\ProductAbstractAvailabilityNotFoundException
     *
     * @return \Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer
     */
    public function getProductAbstractAvailability(int $idProductAbstract, int $idLocale): ProductAbstractAvailabilityTransfer
    {
        $storeTransfer = $this->storeFacade->getCurrentStore();

        $stockNames = $this->stockFacade->getStoreToWarehouseMapping()[$storeTransfer->getName()];
        /** @var int $idStore */
        $idStore = $storeTransfer->requireIdStore()->getIdStore();

        $productAbstractEntity = $this->availabilityQueryContainer
            ->queryAvailabilityAbstractWithStockByIdProductAbstractAndIdLocale(
                $idProductAbstract,
                $idLocale,
                $idStore,
                $stockNames,
            )
            ->findOne();

        if (!$productAbstractEntity) {
            throw new ProductAbstractAvailabilityNotFoundException(
                sprintf('The product abstract availability was not found with this product abstract ID: %d', $idProductAbstract),
            );
        }

        return $this->mapAbstractProductAvailabilityEntityToTransfer($productAbstractEntity);
    }

    /**
     * @param int $idProductAbstract
     * @param int $idLocale
     * @param int $idStore
     *
     * @return \Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer|null
     */
    public function findProductAbstractAvailability(int $idProductAbstract, int $idLocale, int $idStore): ?ProductAbstractAvailabilityTransfer
    {
        $storeTransfer = $this->storeFacade->getStoreById($idStore);

        $stockTypes = $this->stockFacade->getStoreToWarehouseMapping()[$storeTransfer->getName()];
        /** @var int $idStore */
        $idStore = $storeTransfer->requireIdStore()->getIdStore();

        $productAbstractEntity = $this->availabilityQueryContainer
            ->queryAvailabilityAbstractWithStockByIdProductAbstractAndIdLocale(
                $idProductAbstract,
                $idLocale,
                $idStore,
                $stockTypes,
            )
            ->findOne();

        if (!$productAbstractEntity) {
            return null;
        }

        return $this->mapAbstractProductAvailabilityEntityToTransfer($productAbstractEntity);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductConcreteAvailabilityRequestTransfer $productConcreteAvailabilityRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer|null
     */
    public function findProductConcreteAvailability(
        ProductConcreteAvailabilityRequestTransfer $productConcreteAvailabilityRequestTransfer
    ): ?ProductConcreteAvailabilityTransfer {
        $productConcreteAvailabilityRequestTransfer->requireSku();

        $storeTransfer = $this->storeFacade->getCurrentStore();
        /** @var string $sku */
        $sku = $productConcreteAvailabilityRequestTransfer->requireSku()->getSku();
        /** @var int $idStore */
        $idStore = $storeTransfer->requireIdStore()->getIdStore();

        $availabilityEntity = $this->availabilityQueryContainer
            ->queryAvailabilityBySkuAndIdStore($sku, $idStore)
            ->findOne();

        if ($availabilityEntity === null) {
            return null;
        }

        return $this->mapProductConcreteAvailabilityEntityToTransfer($availabilityEntity);
    }

    /**
     * @param string $reservationQuantitySet
     *
     * @return \Spryker\DecimalObject\Decimal
     */
    protected function calculateReservation(string $reservationQuantitySet): Decimal
    {
        $reservationItems = explode(',', $reservationQuantitySet);
        $reservationItems = array_unique($reservationItems);

        return $this->getReservationUniqueValue($reservationItems);
    }

    /**
     * @param array<string> $reservationItems
     *
     * @return \Spryker\DecimalObject\Decimal
     */
    protected function getReservationUniqueValue(array $reservationItems): Decimal
    {
        $reservation = new Decimal(0);
        foreach ($reservationItems as $item) {
            if ((int)strpos($item, ':') === 0) {
                continue;
            }

            [$sku, $quantity] = explode(':', $item);
            if ($sku === '' || !is_numeric($quantity)) {
                continue;
            }

            $reservation = $reservation->add(new Decimal($quantity));
        }

        return $reservation;
    }

    /**
     * @param \Orm\Zed\Availability\Persistence\SpyAvailability $availabilityEntity
     *
     * @return \Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer
     */
    protected function mapProductConcreteAvailabilityEntityToTransfer(SpyAvailability $availabilityEntity)
    {
        return (new ProductConcreteAvailabilityTransfer())
            ->setAvailability($availabilityEntity->getQuantity())
            ->setIsNeverOutOfStock($availabilityEntity->getIsNeverOutOfStock());
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract $productAbstractEntity
     *
     * @return \Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer
     */
    protected function mapAbstractProductAvailabilityEntityToTransfer(SpyProductAbstract $productAbstractEntity)
    {
        $availabilityData = $productAbstractEntity->toArray();
        $availabilityData[ProductAbstractAvailabilityTransfer::IS_NEVER_OUT_OF_STOCK] = $this->getAbstractNeverOutOfStock($productAbstractEntity);
        $availabilityData[ProductAbstractAvailabilityTransfer::AVAILABILITY] = $productAbstractEntity->getVirtualColumn(AvailabilityQueryContainer::AVAILABILITY_QUANTITY);
        $availabilityData[ProductAbstractAvailabilityTransfer::RESERVATION_QUANTITY] = $this->calculateReservation(
            $productAbstractEntity->getVirtualColumn(AvailabilityQueryContainer::RESERVATION_QUANTITY) ?? '',
        );

        return (new ProductAbstractAvailabilityTransfer())->fromArray($availabilityData, true);
    }

    /**
     * @param \Orm\Zed\Product\Persistence\SpyProductAbstract $productAbstractEntity
     *
     * @return bool
     */
    protected function getAbstractNeverOutOfStock(SpyProductAbstract $productAbstractEntity): bool
    {
        $neverOutOfStockSet = explode(',', $productAbstractEntity->getVirtualColumn(AvailabilityQueryContainer::CONCRETE_NEVER_OUT_OF_STOCK_SET) ?? '');

        foreach ($neverOutOfStockSet as $status) {
            if (filter_var($status, FILTER_VALIDATE_BOOLEAN)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param int|null $idStore
     *
     * @return \Generated\Shared\Transfer\StoreTransfer
     */
    protected function getStoreTransfer($idStore = null)
    {
        if ($idStore !== null) {
            return $this->storeFacade->getStoreById($idStore);
        }

        return $this->storeFacade->getCurrentStore();
    }
}
