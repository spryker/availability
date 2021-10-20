<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability;

use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToEventFacadeBridge;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToOmsFacadeBridge;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToProductFacadeBridge;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStockFacadeBridge;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStoreFacadeBridge;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToTouchFacadeBridge;
use Spryker\Zed\Availability\Dependency\QueryContainer\AvailabilityToProductQueryContainerBridge;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;

/**
 * @method \Spryker\Zed\Availability\AvailabilityConfig getConfig()
 */
class AvailabilityDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_EVENT = 'FACADE_EVENT';

    /**
     * @var string
     */
    public const FACADE_OMS = 'FACADE_OMS';

    /**
     * @var string
     */
    public const FACADE_STOCK = 'FACADE_STOCK';

    /**
     * @var string
     */
    public const FACADE_TOUCH = 'FACADE_TOUCH';

    /**
     * @var string
     */
    public const FACADE_STORE = 'FACADE_STORE';

    /**
     * @var string
     */
    public const FACADE_PRODUCT = 'FACADE_PRODUCT';

    /**
     * @var string
     */
    public const PLUGINS_AVAILABILITY_STRATEGY = 'PLUGINS_AVAILABILITY_STRATEGY';

    /**
     * @var string
     */
    public const PLUGINS_BATCH_AVAILABILITY_STRATEGY = 'PLUGINS_BATCH_AVAILABILITY_STRATEGY';

    /**
     * @var string
     */
    public const PLUGINS_CART_ITEM_QUANTITY_COUNTER_STRATEGY = 'PLUGINS_CART_ITEM_QUANTITY_COUNTER_STRATEGY';

    /**
     * @var string
     */
    public const QUERY_CONTAINER_PRODUCT = 'QUERY_CONTAINER_PRODUCT';

    /**
     * @var string
     */
    public const SERVICE_AVAILABILITY = 'SERVICE_AVAILABILITY';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container = $this->addOmsFacade($container);
        $container = $this->addStockFacade($container);
        $container = $this->addTouchFacade($container);
        $container = $this->addStoreFacade($container);
        $container = $this->addEventFacade($container);
        $container = $this->addProductFacade($container);

        $container = $this->addAvailabilityStrategyPlugins($container);
        $container = $this->addCartItemQuantityCounterStrategyPlugins($container);
        $container = $this->addBatchAvailabilityStrategyPlugins($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function providePersistenceLayerDependencies(Container $container)
    {
        $container = $this->addProductQueryContainer($container);
        $container = $this->addAvailabilityService($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function addStoreFacade(Container $container)
    {
        $container->set(static::FACADE_STORE, function (Container $container) {
            return new AvailabilityToStoreFacadeBridge($container->getLocator()->store()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addEventFacade(Container $container): Container
    {
        $container->set(static::FACADE_EVENT, function (Container $container) {
            return new AvailabilityToEventFacadeBridge(
                $container->getLocator()->event()->facade(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addTouchFacade(Container $container)
    {
        $container->set(static::FACADE_TOUCH, function (Container $container) {
            return new AvailabilityToTouchFacadeBridge($container->getLocator()->touch()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStockFacade(Container $container)
    {
        $container->set(static::FACADE_STOCK, function (Container $container) {
            return new AvailabilityToStockFacadeBridge($container->getLocator()->stock()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOmsFacade(Container $container)
    {
        $container->set(static::FACADE_OMS, function (Container $container) {
            return new AvailabilityToOmsFacadeBridge($container->getLocator()->oms()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function addProductFacade(Container $container): Container
    {
        $container->set(static::FACADE_PRODUCT, function (Container $container) {
            return new AvailabilityToProductFacadeBridge($container->getLocator()->product()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAvailabilityStrategyPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_AVAILABILITY_STRATEGY, function () {
            return $this->getAvailabilityStrategyPlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addBatchAvailabilityStrategyPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_BATCH_AVAILABILITY_STRATEGY, function () {
            return $this->getBatchAvailabilityStrategyPlugins();
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCartItemQuantityCounterStrategyPlugins(Container $container): Container
    {
        $container->set(static::PLUGINS_CART_ITEM_QUANTITY_COUNTER_STRATEGY, function () {
            return $this->getCartItemQuantityCounterStrategyPlugins();
        });

        return $container;
    }

    /**
     * @return array<\Spryker\Zed\AvailabilityExtension\Dependency\Plugin\AvailabilityStrategyPluginInterface>
     */
    protected function getAvailabilityStrategyPlugins(): array
    {
        return [];
    }

    /**
     * @return array<\Spryker\Zed\AvailabilityExtension\Dependency\Plugin\AvailabilityStrategyPluginInterface>
     */
    protected function getBatchAvailabilityStrategyPlugins(): array
    {
        return [];
    }

    /**
     * @return array<\Spryker\Zed\AvailabilityExtension\Dependency\Plugin\CartItemQuantityCounterStrategyPluginInterface>
     */
    protected function getCartItemQuantityCounterStrategyPlugins(): array
    {
        return [];
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addProductQueryContainer(Container $container)
    {
        $container->set(static::QUERY_CONTAINER_PRODUCT, function (Container $container) {
            return new AvailabilityToProductQueryContainerBridge($container->getLocator()->product()->queryContainer());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addAvailabilityService(Container $container): Container
    {
        $container->set(static::SERVICE_AVAILABILITY, function (Container $container) {
            return $container->getLocator()->availability()->service();
        });

        return $container;
    }
}
