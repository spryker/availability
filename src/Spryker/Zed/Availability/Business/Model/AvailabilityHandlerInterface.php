<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Business\Model;

use Generated\Shared\Transfer\DynamicEntityPostEditRequestTransfer;
use Generated\Shared\Transfer\DynamicEntityPostEditResponseTransfer;
use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\DecimalObject\Decimal;

interface AvailabilityHandlerInterface
{
    public function updateAvailability(string $concreteSku): void;

    /**
     * @param int $idAvailabilityAbstract
     *
     * @return void
     */
    public function touchAvailabilityAbstract($idAvailabilityAbstract);

    public function saveAndTouchAvailability(string $concreteSku, Decimal $quantity, StoreTransfer $storeTransfer): int;

    public function updateProductConcreteAvailabilityById(
        int $idProductConcrete,
        StoreTransfer $storeTransfer
    ): ProductConcreteAvailabilityTransfer;

    public function updateProductConcreteAvailabilityBySku(
        string $concreteSku,
        StoreTransfer $storeTransfer
    ): ProductConcreteAvailabilityTransfer;

    public function updateProductAbstractAvailabilityBySku(
        string $abstractSku,
        StoreTransfer $storeTransfer
    ): ProductAbstractAvailabilityTransfer;

    public function updateAvailabilityByDynamicEntityRequest(
        DynamicEntityPostEditRequestTransfer $dynamicEntityPostEditRequestTransfer
    ): DynamicEntityPostEditResponseTransfer;
}
