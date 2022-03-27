<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Persistence;

use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;
use Generated\Shared\Transfer\ProductAvailabilityCriteriaTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityCollectionTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Orm\Zed\Availability\Persistence\Map\SpyAvailabilityAbstractTableMap;
use Orm\Zed\Availability\Persistence\Map\SpyAvailabilityTableMap;
use Orm\Zed\Availability\Persistence\SpyAvailabilityQuery;
use Orm\Zed\Product\Persistence\Map\SpyProductAbstractTableMap;
use Orm\Zed\Product\Persistence\Map\SpyProductTableMap;
use Spryker\Zed\Availability\Persistence\Exception\AvailabilityAbstractNotFoundException;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;
use Spryker\Zed\PropelOrm\Business\Runtime\ActiveQuery\Criteria;

/**
 * @method \Spryker\Zed\Availability\Persistence\AvailabilityPersistenceFactory getFactory()
 */
class AvailabilityRepository extends AbstractRepository implements AvailabilityRepositoryInterface
{
    /**
     * @var string
     */
    protected const COL_ID_PRODUCT = 'id_product';

    /**
     * @param int $idProductConcrete
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer|null
     */
    public function findProductConcreteAvailabilityByIdProductConcreteAndStore(
        int $idProductConcrete,
        StoreTransfer $storeTransfer
    ): ?ProductConcreteAvailabilityTransfer {
        $storeTransfer->requireIdStore();

        /** @var literal-string $where */
        $where = sprintf('%s = %d', SpyProductTableMap::COL_ID_PRODUCT, $idProductConcrete);
        $availabilityEntity = $this->getFactory()
            ->createSpyAvailabilityQuery()
            ->filterByFkStore($storeTransfer->getIdStore())
            ->addJoin(SpyAvailabilityTableMap::COL_SKU, SpyProductTableMap::COL_SKU, Criteria::INNER_JOIN)
            ->where($where)
            ->findOne();

        if ($availabilityEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createAvailabilityMapper()
            ->mapAvailabilityEntityToProductConcreteAvailabilityTransfer(
                $availabilityEntity,
                new ProductConcreteAvailabilityTransfer(),
            );
    }

    /**
     * @param array<int> $productConcreteIds
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer>
     */
    public function getMappedProductConcreteAvailabilitiesByProductConcreteIds(
        array $productConcreteIds,
        StoreTransfer $storeTransfer
    ): array {
        $storeTransfer->requireIdStore();

        /** @var literal-string $where */
        $where = sprintf('%s IN (%s)', SpyProductTableMap::COL_ID_PRODUCT, implode(',', $productConcreteIds));
        $availabilityEntities = $this->getFactory()
            ->createSpyAvailabilityQuery()
            ->filterByFkStore($storeTransfer->getIdStore())
            ->addJoin(SpyAvailabilityTableMap::COL_SKU, SpyProductTableMap::COL_SKU, Criteria::INNER_JOIN)
            ->where($where)
            ->withColumn(SpyProductTableMap::COL_ID_PRODUCT, static::COL_ID_PRODUCT)
            ->find()
            ->toKeyIndex(static::COL_ID_PRODUCT);

        $productConcreteAvailabilityTransfers = [];
        $availabilityMapper = $this->getFactory()->createAvailabilityMapper();

        foreach ($availabilityEntities as $idProductConcrete => $availabilityEntity) {
            $productConcreteAvailabilityTransfer = $availabilityMapper->mapAvailabilityEntityToProductConcreteAvailabilityTransfer(
                $availabilityEntity,
                new ProductConcreteAvailabilityTransfer(),
            );

            $productConcreteAvailabilityTransfers[$idProductConcrete] = $productConcreteAvailabilityTransfer;
        }

        return $productConcreteAvailabilityTransfers;
    }

    /**
     * @param array<string> $concreteSkus
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return array<\Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer>
     */
    public function findProductConcreteAvailabilityBySkusAndStore(
        array $concreteSkus,
        StoreTransfer $storeTransfer
    ): array {
        $storeTransfer->requireIdStore();

        $availabilityEntities = $this->getFactory()
            ->createSpyAvailabilityQuery()
            ->filterByFkStore($storeTransfer->getIdStore())
            ->filterBySku($concreteSkus, Criteria::IN)
            ->find();

        if (!count($availabilityEntities)) {
            return [];
        }

        return $this->mapAvailabilityEntityToProductConcreteAvailabilityTransfers($availabilityEntities->getArrayCopy());
    }

    /**
     * @param array<\Orm\Zed\Availability\Persistence\SpyAvailability> $availabilityEntities
     *
     * @return array<\Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer>
     */
    protected function mapAvailabilityEntityToProductConcreteAvailabilityTransfers(array $availabilityEntities): array
    {
        $productConcreteAvailabilityTransfers = [];
        $availabilityMapper = $this->getFactory()->createAvailabilityMapper();
        foreach ($availabilityEntities as $availabilityEntity) {
            $productConcreteAvailabilityTransfers[] = $availabilityMapper->mapAvailabilityEntityToProductConcreteAvailabilityTransfer(
                $availabilityEntity,
                new ProductConcreteAvailabilityTransfer(),
            );
        }

        return $productConcreteAvailabilityTransfers;
    }

    /**
     * @param string $concreteSku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer|null
     */
    public function findProductConcreteAvailabilityBySkuAndStore(
        string $concreteSku,
        StoreTransfer $storeTransfer
    ): ?ProductConcreteAvailabilityTransfer {
        $storeTransfer->requireIdStore();

        $availabilityEntity = $this->getFactory()
            ->createSpyAvailabilityQuery()
            ->filterByFkStore($storeTransfer->getIdStore())
            ->filterBySku($concreteSku)
            ->findOne();

        if ($availabilityEntity === null) {
            return null;
        }

        return $this->getFactory()
            ->createAvailabilityMapper()
            ->mapAvailabilityEntityToProductConcreteAvailabilityTransfer(
                $availabilityEntity,
                new ProductConcreteAvailabilityTransfer(),
            );
    }

    /**
     * @param string $abstractSku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer|null
     */
    public function findProductAbstractAvailabilityBySkuAndStore(
        string $abstractSku,
        StoreTransfer $storeTransfer
    ): ?ProductAbstractAvailabilityTransfer {
        $storeTransfer->requireIdStore();

        /** @var \Orm\Zed\Availability\Persistence\SpyAvailabilityAbstractQuery $query */
        $query = $this->getFactory()
            ->createSpyAvailabilityAbstractQuery()
            ->filterByFkStore($storeTransfer->getIdStore())
            ->filterByAbstractSku($abstractSku)
            ->useSpyAvailabilityQuery()
                ->filterByFkStore($storeTransfer->getIdStore())
            ->endUse()
            ->select([
                SpyAvailabilityAbstractTableMap::COL_ABSTRACT_SKU,
            ])->withColumn(SpyAvailabilityAbstractTableMap::COL_ABSTRACT_SKU, ProductAbstractAvailabilityTransfer::SKU)
            ->withColumn(SpyAvailabilityAbstractTableMap::COL_QUANTITY, ProductAbstractAvailabilityTransfer::AVAILABILITY)
            ->withColumn('GROUP_CONCAT(' . SpyAvailabilityTableMap::COL_IS_NEVER_OUT_OF_STOCK . ')', ProductAbstractAvailabilityTransfer::IS_NEVER_OUT_OF_STOCK);

        /** @var array|null $availabilityAbstractEntityArray */
        $availabilityAbstractEntityArray = $query->groupByAbstractSku()
            ->findOne();

        if ($availabilityAbstractEntityArray === null) {
            return null;
        }

        return $this->getFactory()
            ->createAvailabilityMapper()
            ->mapAvailabilityEntityToProductAbstractAvailabilityTransfer(
                $availabilityAbstractEntityArray,
                new ProductAbstractAvailabilityTransfer(),
            );
    }

    /**
     * @param string $abstractSku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @throws \Spryker\Zed\Availability\Persistence\Exception\AvailabilityAbstractNotFoundException
     *
     * @return int
     */
    public function findIdProductAbstractAvailabilityBySku(
        string $abstractSku,
        StoreTransfer $storeTransfer
    ): int {
        /** @var int|null $idAvailabilityAbstract */
        $idAvailabilityAbstract = $this->getFactory()
            ->createSpyAvailabilityAbstractQuery()
            ->filterByAbstractSku($abstractSku)
            ->filterByFkStore($storeTransfer->getIdStore())
            ->select(SpyAvailabilityAbstractTableMap::COL_ID_AVAILABILITY_ABSTRACT)
            ->findOne();

        if ($idAvailabilityAbstract === null) {
            throw new AvailabilityAbstractNotFoundException(
                'You cannot update concrete availability without updating abstract availability first',
            );
        }

        return $idAvailabilityAbstract;
    }

    /**
     * @param string $concreteSku
     *
     * @return string|null
     */
    public function getAbstractSkuFromProductConcrete(string $concreteSku): ?string
    {
        return $this->getFactory()
            ->getProductQueryContainer()
            ->queryProductAbstract()
            ->useSpyProductQuery()
                ->filterBySku($concreteSku)
            ->endUse()
            ->select(SpyProductAbstractTableMap::COL_SKU)
            ->findOne();
    }

    /**
     * @param int $idProductConcrete
     *
     * @return string|null
     */
    public function getProductConcreteSkuByConcreteId(int $idProductConcrete): ?string
    {
        return $this->getFactory()
            ->getProductQueryContainer()
            ->queryProductAbstract()
            ->useSpyProductQuery()
                ->filterByIdProduct($idProductConcrete)
            ->endUse()
            ->select(SpyProductTableMap::COL_SKU)
            ->findOne();
    }

    /**
     * @param string $concreteSku
     *
     * @return array<\Generated\Shared\Transfer\StoreTransfer>
     */
    public function getStoresWhereProductAvailabilityIsDefined(string $concreteSku): array
    {
        $availabilityEntities = $this->getFactory()
            ->createSpyAvailabilityQuery()
            ->joinWithStore(Criteria::LEFT_JOIN)
            ->filterBySku($concreteSku)
            ->find();

        $storeEntities = [];
        foreach ($availabilityEntities as $availabilityEntity) {
            /** @var \Orm\Zed\Store\Persistence\SpyStore $storeEntity */
            $storeEntity = $availabilityEntity->getStore();

            $storeEntities[] = $storeEntity;
        }

        return $this->getFactory()
            ->createStoreMapper()
            ->mapStoreEntitiesToStoreTransfers($storeEntities);
    }

    /**
     * @param string $productAbstractSku
     *
     * @return array<string>
     */
    public function getProductConcreteSkusByAbstractProductSku(string $productAbstractSku): array
    {
        return $this->getFactory()
            ->getProductQueryContainer()
            ->queryProductAbstract()
            ->filterBySku($productAbstractSku)
            ->joinWithSpyProduct(Criteria::LEFT_JOIN)
            ->select(SpyProductTableMap::COL_SKU)
            ->find()
            ->getData();
    }

    /**
     * @param \Generated\Shared\Transfer\ProductAvailabilityCriteriaTransfer $productAvailabilityCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteAvailabilityCollectionTransfer
     */
    public function getProductConcreteAvailabilityCollection(
        ProductAvailabilityCriteriaTransfer $productAvailabilityCriteriaTransfer
    ): ProductConcreteAvailabilityCollectionTransfer {
        $availabilityQuery = $this->getFactory()->createSpyAvailabilityQuery();
        $availabilityQuery = $this->applyFilters($availabilityQuery, $productAvailabilityCriteriaTransfer);

        $availabilityEntities = $availabilityQuery->find();

        return $this->getFactory()
            ->createAvailabilityMapper()
            ->mapAvailabilityEntitiesToProductConcreteAvailabilityCollectionTransfer(
                $availabilityEntities,
                new ProductConcreteAvailabilityCollectionTransfer(),
            );
    }

    /**
     * @param \Orm\Zed\Availability\Persistence\SpyAvailabilityQuery $availabilityQuery
     * @param \Generated\Shared\Transfer\ProductAvailabilityCriteriaTransfer $productAvailabilityCriteriaTransfer
     *
     * @return \Orm\Zed\Availability\Persistence\SpyAvailabilityQuery
     */
    protected function applyFilters(
        SpyAvailabilityQuery $availabilityQuery,
        ProductAvailabilityCriteriaTransfer $productAvailabilityCriteriaTransfer
    ): SpyAvailabilityQuery {
        if ($productAvailabilityCriteriaTransfer->getProductConcreteSkus()) {
            $availabilityQuery->filterBySku_In($productAvailabilityCriteriaTransfer->getProductConcreteSkus());
        }

        if ($productAvailabilityCriteriaTransfer->getStoreIds()) {
            $availabilityQuery->filterByFkStore_In($productAvailabilityCriteriaTransfer->getStoreIds());
        }

        return $availabilityQuery;
    }
}
