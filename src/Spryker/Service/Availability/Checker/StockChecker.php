<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Service\Availability\Checker;

use Spryker\Service\Availability\AvailabilityConfig;

class StockChecker implements StockCheckerInterface
{
    /**
     * @var \Spryker\Service\Availability\AvailabilityConfig
     */
    protected $availabilityConfig;

    public function __construct(AvailabilityConfig $availabilityConfig)
    {
        $this->availabilityConfig = $availabilityConfig;
    }

    public function isAbstractProductNeverOutOfStock(string $productConcretesNeverOutOfStockSet): bool
    {
        return (bool)preg_match($this->availabilityConfig->getIsNeverOutOfStockPattern(), $productConcretesNeverOutOfStockSet);
    }
}
