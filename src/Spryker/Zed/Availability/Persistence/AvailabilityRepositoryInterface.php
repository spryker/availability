<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Persistence;

use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;
use Generated\Shared\Transfer\ProductAvailabilityCriteriaTransfer;
use Generated\Shared\Transfer\ProductAvailabilityDataTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityCollectionTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Generated\Shared\Transfer\StoreTransfer;

interface AvailabilityRepositoryInterface
{
    public function findProductConcreteAvailabilityByIdProductConcreteAndStore(
        int $idProductConcrete,
        StoreTransfer $storeTransfer
    ): ?ProductConcreteAvailabilityTransfer;

    /**
     * @param array<int> $productConcreteIds
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer>
     */
    public function getMappedProductConcreteAvailabilitiesByProductConcreteIds(
        array $productConcreteIds,
        StoreTransfer $storeTransfer
    ): array;

    /**
     * @param array<string> $concreteSkus
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer>
     */
    public function findProductConcreteAvailabilityBySkusAndStore(
        array $concreteSkus,
        StoreTransfer $storeTransfer
    ): array;

    public function findProductConcreteAvailabilityBySkuAndStore(
        string $concreteSku,
        StoreTransfer $storeTransfer
    ): ?ProductConcreteAvailabilityTransfer;

    public function findProductAbstractAvailabilityBySkuAndStore(
        string $abstractSku,
        StoreTransfer $storeTransfer
    ): ?ProductAbstractAvailabilityTransfer;

    public function findIdProductAbstractAvailabilityBySku(
        string $abstractSku,
        StoreTransfer $storeTransfer
    ): int;

    public function getAbstractSkuFromProductConcrete(string $concreteSku): ?string;

    public function getProductConcreteSkuByConcreteId(int $idProductConcrete): ?string;

    /**
     * @param string $productAbstractSku
     *
     * @return array<string>
     */
    public function getProductConcreteSkusByAbstractProductSku(string $productAbstractSku): array;

    /**
     * @param string $concreteSku
     *
     * @return array<\Generated\Shared\Transfer\StoreTransfer>
     */
    public function getStoresWhereProductAvailabilityIsDefined(string $concreteSku): array;

    public function getProductConcreteAvailabilityCollection(
        ProductAvailabilityCriteriaTransfer $productAvailabilityCriteriaTransfer
    ): ProductConcreteAvailabilityCollectionTransfer;

    /**
     * @param string $concreteSku
     *
     * @throws \Spryker\Zed\Propel\Business\Exception\AmbiguousComparisonException
     *
     * @return \Generated\Shared\Transfer\ProductAvailabilityDataTransfer
     */
    public function getProductConcreteWithAvailability(string $concreteSku): ProductAvailabilityDataTransfer;
}
