<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Business\Model;

use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;
use Generated\Shared\Transfer\ProductAvailabilityCriteriaTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Generated\Shared\Transfer\StoreTransfer;

interface ProductAvailabilityReaderInterface
{
    public function findOrCreateProductAbstractAvailabilityBySkuForStore(
        string $abstractSku,
        StoreTransfer $storeTransfer
    ): ?ProductAbstractAvailabilityTransfer;

    public function findOrCreateProductConcreteAvailabilityBySkuForStore(
        string $concreteSku,
        StoreTransfer $storeTransfer,
        ?ProductAvailabilityCriteriaTransfer $productAvailabilityCriteriaTransfer = null
    ): ?ProductConcreteAvailabilityTransfer;
}
