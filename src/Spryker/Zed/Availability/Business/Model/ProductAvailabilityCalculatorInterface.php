<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Business\Model;

use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\DecimalObject\Decimal;

interface ProductAvailabilityCalculatorInterface
{
    /**
     * @param string $concreteSku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param array<\Generated\Shared\Transfer\StockProductTransfer> $stockProductTransfers
     *
     * @return \Spryker\DecimalObject\Decimal
     */
    public function calculateAvailabilityForProductConcrete(
        string $concreteSku,
        StoreTransfer $storeTransfer,
        array $stockProductTransfers = []
    ): Decimal;

    /**
     * @param string $abstractSku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Spryker\DecimalObject\Decimal
     */
    public function calculateAvailabilityForProductAbstract(string $abstractSku, StoreTransfer $storeTransfer): Decimal;

    /**
     * @param string $concreteSku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return bool
     */
    public function isNeverOutOfStockForStore(string $concreteSku, StoreTransfer $storeTransfer): bool;

    /**
     * @param string $concreteSku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer
     */
    public function getCalculatedProductConcreteAvailabilityTransfer(string $concreteSku, StoreTransfer $storeTransfer): ProductConcreteAvailabilityTransfer;

    /**
     * @param string $abstractSku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @throws \Spryker\Zed\Availability\Business\Exception\ProductNotFoundException
     *
     * @return \Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer
     */
    public function getCalculatedProductAbstractAvailabilityTransfer(string $abstractSku, StoreTransfer $storeTransfer): ProductAbstractAvailabilityTransfer;
}
