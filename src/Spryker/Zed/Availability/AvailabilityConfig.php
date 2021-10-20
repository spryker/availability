<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class AvailabilityConfig extends AbstractBundleConfig
{
    /**
     * @var int
     */
    public const ERROR_CODE_PRODUCT_UNAVAILABLE = 4002;

    /**
     * @var string
     */
    public const RESOURCE_TYPE_AVAILABILITY_ABSTRACT = 'availability_abstract';

    /**
     * @var string
     */
    protected const ERROR_TYPE_AVAILABILITY = 'Availability';

    /**
     * @var string
     */
    protected const PARAMETER_PRODUCT_SKU_AVAILABILITY = '%sku%';

    /**
     * @api
     *
     * @return int
     */
    public function getProductUnavailableErrorCode()
    {
        return static::ERROR_CODE_PRODUCT_UNAVAILABLE;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getAvailabilityErrorType(): string
    {
        return static::ERROR_TYPE_AVAILABILITY;
    }

    /**
     * @api
     *
     * @return string
     */
    public function getAvailabilityProductSkuParameter(): string
    {
        return static::PARAMETER_PRODUCT_SKU_AVAILABILITY;
    }
}
