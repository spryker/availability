<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Persistence\Mapper;

use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;
use Generated\Shared\Transfer\ProductAvailabilityDataTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityCollectionTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Orm\Zed\Availability\Persistence\SpyAvailability;
use Orm\Zed\Product\Persistence\SpyProduct;
use Propel\Runtime\Collection\Collection;

interface AvailabilityMapperInterface
{
    public function mapAvailabilityEntityToProductConcreteAvailabilityTransfer(
        SpyAvailability $availabilityEntity,
        ProductConcreteAvailabilityTransfer $productConcreteAvailabilityTransfer
    ): ProductConcreteAvailabilityTransfer;

    public function mapAvailabilityEntityToProductAbstractAvailabilityTransfer(
        array $availabilityAbstractEntityArray,
        ProductAbstractAvailabilityTransfer $productAbstractAvailabilityTransfer
    ): ProductAbstractAvailabilityTransfer;

    /**
     * @param \Propel\Runtime\Collection\Collection<\Orm\Zed\Availability\Persistence\SpyAvailability> $availabilityEntities
     * @param \Generated\Shared\Transfer\ProductConcreteAvailabilityCollectionTransfer $productConcreteAvailabilityCollectionTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteAvailabilityCollectionTransfer
     */
    public function mapAvailabilityEntitiesToProductConcreteAvailabilityCollectionTransfer(
        Collection $availabilityEntities,
        ProductConcreteAvailabilityCollectionTransfer $productConcreteAvailabilityCollectionTransfer
    ): ProductConcreteAvailabilityCollectionTransfer;

    public function mapAvailabilityEntitiesAndProductConcreteEntityToProductAvailabilityDataTransfer(
        Collection $availabilityEntities,
        ?SpyProduct $productConcreteEntity,
        ProductAvailabilityDataTransfer $productAvailabilityDataTransfer
    ): ProductAvailabilityDataTransfer;
}
