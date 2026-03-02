<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Business\Model;

use Generated\Shared\Transfer\ProductAvailabilityCriteriaTransfer;
use Generated\Shared\Transfer\SellableItemsRequestTransfer;
use Generated\Shared\Transfer\SellableItemsResponseTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\DecimalObject\Decimal;

interface SellableInterface
{
    public function areProductsSellableForStore(
        SellableItemsRequestTransfer $sellableItemsRequestTransfer
    ): SellableItemsResponseTransfer;

    /**
     * @deprecated Use {@link \Spryker\Zed\Availability\Business\Model\SellableInterface::areProductsSellableForStore()} instead.
     * Exists for BC reasons and should be removed in the next major.
     *
     * @param \Generated\Shared\Transfer\SellableItemsRequestTransfer $sellableItemsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\SellableItemsResponseTransfer
     */
    public function areProductsSellableForStoreWithDefaultBatchStrategy(
        SellableItemsRequestTransfer $sellableItemsRequestTransfer
    ): SellableItemsResponseTransfer;

    public function areProductConcretesSellableForStore(
        SellableItemsRequestTransfer $sellableItemsRequestTransfer,
        SellableItemsResponseTransfer $sellableItemsResponseTransfer
    ): SellableItemsResponseTransfer;

    public function isProductSellableForStore(
        string $concreteSku,
        Decimal $quantity,
        StoreTransfer $storeTransfer,
        ?ProductAvailabilityCriteriaTransfer $productAvailabilityCriteriaTransfer = null
    ): bool;

    public function isProductConcreteAvailable(int $idProductConcrete): bool;
}
