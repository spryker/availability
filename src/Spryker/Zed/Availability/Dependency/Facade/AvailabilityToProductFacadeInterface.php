<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Dependency\Facade;

use Generated\Shared\Transfer\ProductConcreteTransfer;

interface AvailabilityToProductFacadeInterface
{
    /**
     * @param string $sku
     *
     * @return bool
     */
    public function hasProductAbstract($sku);

    /**
     * @param string $sku
     *
     * @return bool
     */
    public function hasProductConcrete($sku);

    public function findProductConcreteById(int $idProduct): ?ProductConcreteTransfer;

    /**
     * @param array<int> $productIds
     *
     * @return array<mixed>
     */
    public function getProductConcreteSkusByConcreteIds(array $productIds): array;
}
