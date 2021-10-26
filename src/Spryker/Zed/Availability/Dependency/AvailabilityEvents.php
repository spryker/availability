<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Dependency;

interface AvailabilityEvents
{
    /**
     * Specification
     * - This events will be used for spy_availability_abstract publishing
     *
     * @api
     *
     * @var string
     */
    public const AVAILABILITY_ABSTRACT_PUBLISH = 'Entity.spy_availability_abstract.publish';

    /**
     * Specification
     * - This events will be used for spy_availability_abstract un-publishing
     *
     * @api
     *
     * @var string
     */
    public const AVAILABILITY_ABSTRACT_UNPUBLISH = 'Entity.spy_availability_abstract.unpublish';

    /**
     * Specification
     * - This events will be used for spy_availability_abstract entity creation
     *
     * @api
     *
     * @var string
     */
    public const ENTITY_SPY_AVAILABILITY_ABSTRACT_CREATE = 'Entity.spy_availability_abstract.create';

    /**
     * Specification
     * - This events will be used for spy_availability_abstract entity changes
     *
     * @api
     *
     * @var string
     */
    public const ENTITY_SPY_AVAILABILITY_ABSTRACT_UPDATE = 'Entity.spy_availability_abstract.update';

    /**
     * Specification
     * - This events will be used for spy_availability_abstract entity deletion
     *
     * @api
     *
     * @var string
     */
    public const ENTITY_SPY_AVAILABILITY_ABSTRACT_DELETE = 'Entity.spy_availability_abstract.delete';

    /**
     * Specification
     * - This events will be used for spy_availability entity changes
     *
     * @api
     *
     * @var string
     */
    public const ENTITY_SPY_AVAILABILITY_UPDATE = 'Entity.spy_availability.update';

    /**
     * Specification:
     * - This event will be used for notifying subscribers if product is available again.
     *
     * @var string
     */
    public const AVAILABILITY_NOTIFICATION = 'availability_notification';

    /**
     * Specification:
     * - This event will be used for Availability publishing by ProductAbstractId.
     *
     * @api
     *
     * @var string
     */
    public const AVAILABILITY_PRODUCT_ABSTRACT_PUBLISH = 'Availability.product_abstract.publish';

    /**
     * Specification:
     * - This event will be used for Availability unpublishing by ProductAbstractId.
     *
     * @api
     *
     * @var string
     */
    public const AVAILABILITY_PRODUCT_ABSTRACT_UNPUBLISH = 'Availability.product_abstract.unpublish';
}
