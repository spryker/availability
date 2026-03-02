<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Availability;

use Spryker\Client\Availability\Dependency\Client\AvailabilityToLocaleInterface;
use Spryker\Client\Availability\Dependency\Client\AvailabilityToStoreClientInterface;
use Spryker\Client\Availability\Dependency\Client\AvailabilityToZedRequestClientInterface;
use Spryker\Client\Availability\KeyBuilder\AvailabilityResourceKeyBuilder;
use Spryker\Client\Availability\Storage\AvailabilityStorage;
use Spryker\Client\Availability\Storage\AvailabilityStorageInterface;
use Spryker\Client\Availability\Zed\AvailabilityStub;
use Spryker\Client\Availability\Zed\AvailabilityStubInterface;
use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Shared\KeyBuilder\KeyBuilderInterface;

class AvailabilityFactory extends AbstractFactory
{
    public function createCurrentLocaleAvailabilityStorage(): AvailabilityStorageInterface
    {
        return new AvailabilityStorage(
            $this->getStorage(),
            $this->createKeyBuilder(),
            $this->getLocaleClient()->getCurrentLocale(),
            $this->getStoreClient(),
        );
    }

    /**
     * @deprecated Will be removed without replacement.
     *
     * @return \Spryker\Client\Availability\Zed\AvailabilityStubInterface
     */
    public function createAvailabilityStub(): AvailabilityStubInterface
    {
        return new AvailabilityStub($this->getZedRequestClient());
    }

    /**
     * @return \Spryker\Client\Availability\Dependency\Client\AvailabilityToStorageInterface
     */
    public function getStorage()
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::KV_STORAGE);
    }

    public function getZedRequestClient(): AvailabilityToZedRequestClientInterface
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::CLIENT_ZED_REQUEST);
    }

    public function createKeyBuilder(): KeyBuilderInterface
    {
        return new AvailabilityResourceKeyBuilder();
    }

    public function getLocaleClient(): AvailabilityToLocaleInterface
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::CLIENT_LOCALE);
    }

    public function getStoreClient(): AvailabilityToStoreClientInterface
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::CLIENT_STORE);
    }
}
