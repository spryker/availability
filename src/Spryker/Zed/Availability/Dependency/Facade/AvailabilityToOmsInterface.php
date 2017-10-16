<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Dependency\Facade;

interface AvailabilityToOmsInterface
{
    /**
     * @deprecated Using this method will affect the performance,
     * use AvailabilityToOmsInterface::getOmsReservedProductQuantitiesForSku() instead.
     *
     * @param string $sku
     *
     * @return int
     */
    public function sumReservedProductQuantitiesForSku($sku);

    /**
     * @param string $sku
     *
     * @return int
     */
    public function getOmsReservedProductQuantitiesForSku($sku);
}
