<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Dependency\Facade;

class AvailabilityToStoreFacadeBridge implements AvailabilityToStoreFacadeInterface
{
    /**
     * @var \Spryker\Zed\Store\Business\StoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @param \Spryker\Zed\Store\Business\StoreFacadeInterface $storeFacade
     */
    public function __construct($storeFacade)
    {
        $this->storeFacade = $storeFacade;
    }

    /**
     * @param int $idStore
     *
     * @return \Generated\Shared\Transfer\StoreTransfer
     */
    public function getStoreById($idStore)
    {
        return $this->storeFacade->getStoreById($idStore);
    }

    /**
     * @param bool $fallbackToDefault
     *
     * @return \Generated\Shared\Transfer\StoreTransfer
     */
    public function getCurrentStore(bool $fallbackToDefault = false)
    {
        return $this->storeFacade->getCurrentStore($fallbackToDefault);
    }

    /**
     * @param string $storeName
     *
     * @return \Generated\Shared\Transfer\StoreTransfer
     */
    public function getStoreByName($storeName)
    {
        return $this->storeFacade->getStoreByName($storeName);
    }
}
