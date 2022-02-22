<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Business;

use Spryker\Zed\Availability\AvailabilityDependencyProvider;
use Spryker\Zed\Availability\Business\Expander\Wishlist\AvailabilityWishlistItemExpander;
use Spryker\Zed\Availability\Business\Expander\Wishlist\AvailabilityWishlistItemExpanderInterface;
use Spryker\Zed\Availability\Business\Model\AvailabilityHandler;
use Spryker\Zed\Availability\Business\Model\AvailabilityHandlerInterface;
use Spryker\Zed\Availability\Business\Model\ProductAvailabilityCalculator;
use Spryker\Zed\Availability\Business\Model\ProductAvailabilityCalculatorInterface;
use Spryker\Zed\Availability\Business\Model\ProductAvailabilityReader;
use Spryker\Zed\Availability\Business\Model\ProductAvailabilityReaderInterface;
use Spryker\Zed\Availability\Business\Model\ProductReservationReader;
use Spryker\Zed\Availability\Business\Model\ProductReservationReaderInterface;
use Spryker\Zed\Availability\Business\Model\ProductsAvailableCheckoutPreCondition;
use Spryker\Zed\Availability\Business\Model\ProductsAvailableCheckoutPreConditionInterface;
use Spryker\Zed\Availability\Business\Model\Sellable;
use Spryker\Zed\Availability\Business\Model\SellableInterface;
use Spryker\Zed\Availability\Business\Reader\AvailabilityReader;
use Spryker\Zed\Availability\Business\Reader\AvailabilityReaderInterface;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToEventFacadeInterface;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToOmsFacadeInterface;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToProductFacadeInterface;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStockFacadeInterface;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStoreFacadeInterface;
use Spryker\Zed\Availability\Dependency\Facade\AvailabilityToTouchFacadeInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \Spryker\Zed\Availability\AvailabilityConfig getConfig()
 * @method \Spryker\Zed\Availability\Persistence\AvailabilityQueryContainerInterface getQueryContainer()
 * @method \Spryker\Zed\Availability\Persistence\AvailabilityEntityManagerInterface getEntityManager()
 * @method \Spryker\Zed\Availability\Persistence\AvailabilityRepositoryInterface getRepository()
 */
class AvailabilityBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return \Spryker\Zed\Availability\Business\Model\SellableInterface
     */
    public function createSellableModel(): SellableInterface
    {
        return new Sellable(
            $this->getRepository(),
            $this->createAvailabilityHandler(),
            $this->getStoreFacade(),
            $this->getAvailabilityStrategyPlugins(),
            $this->getBatchAvailabilityStrategyPlugins(),
        );
    }

    /**
     * @return \Spryker\Zed\Availability\Business\Model\ProductAvailabilityCalculatorInterface
     */
    public function createProductAvailabilityCalculator(): ProductAvailabilityCalculatorInterface
    {
        return new ProductAvailabilityCalculator(
            $this->getRepository(),
            $this->getOmsFacade(),
            $this->getStockFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\Availability\Business\Model\AvailabilityHandlerInterface
     */
    public function createAvailabilityHandler(): AvailabilityHandlerInterface
    {
        return new AvailabilityHandler(
            $this->getRepository(),
            $this->getEntityManager(),
            $this->createProductAvailabilityCalculator(),
            $this->getTouchFacade(),
            $this->getStockFacade(),
            $this->getEventFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\Availability\Business\Model\ProductAvailabilityReaderInterface
     */
    public function createProductAvailabilityReader(): ProductAvailabilityReaderInterface
    {
        return new ProductAvailabilityReader(
            $this->getRepository(),
            $this->createAvailabilityHandler(),
            $this->getStoreFacade(),
            $this->getProductFacade(),
            $this->getAvailabilityStrategyPlugins(),
        );
    }

    /**
     * @deprecated Use {@link createProductAvailabilityReader()} instead.
     *
     * @return \Spryker\Zed\Availability\Business\Model\ProductReservationReaderInterface
     */
    public function createProductReservationReader(): ProductReservationReaderInterface
    {
        return new ProductReservationReader(
            $this->getQueryContainer(),
            $this->getStockFacade(),
            $this->getStoreFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\Availability\Business\Reader\AvailabilityReaderInterface
     */
    public function createAvailabilityReader(): AvailabilityReaderInterface
    {
        return new AvailabilityReader(
            $this->getRepository(),
            $this->getStoreFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\Availability\Business\Expander\Wishlist\AvailabilityWishlistItemExpanderInterface
     */
    public function createAvailabilityWishlistItemExpander(): AvailabilityWishlistItemExpanderInterface
    {
        return new AvailabilityWishlistItemExpander(
            $this->createProductAvailabilityReader(),
            $this->createSellableModel(),
            $this->getStoreFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStockFacadeInterface
     */
    public function getStockFacade(): AvailabilityToStockFacadeInterface
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::FACADE_STOCK);
    }

    /**
     * @return \Spryker\Zed\Availability\Dependency\Facade\AvailabilityToOmsFacadeInterface
     */
    public function getOmsFacade(): AvailabilityToOmsFacadeInterface
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::FACADE_OMS);
    }

    /**
     * @return \Spryker\Zed\Availability\Dependency\Facade\AvailabilityToTouchFacadeInterface
     */
    public function getTouchFacade(): AvailabilityToTouchFacadeInterface
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::FACADE_TOUCH);
    }

    /**
     * @return \Spryker\Zed\Availability\Business\Model\ProductsAvailableCheckoutPreConditionInterface
     */
    public function createProductsAvailablePreCondition(): ProductsAvailableCheckoutPreConditionInterface
    {
        return new ProductsAvailableCheckoutPreCondition(
            $this->createSellableModel(),
            $this->getConfig(),
            $this->getCartItemQuantityCounterStrategyPlugins(),
            $this->getStoreFacade(),
        );
    }

    /**
     * @return \Spryker\Zed\Availability\Dependency\Facade\AvailabilityToStoreFacadeInterface
     */
    public function getStoreFacade(): AvailabilityToStoreFacadeInterface
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::FACADE_STORE);
    }

    /**
     * @return \Spryker\Zed\Availability\Dependency\Facade\AvailabilityToEventFacadeInterface
     */
    public function getEventFacade(): AvailabilityToEventFacadeInterface
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::FACADE_EVENT);
    }

    /**
     * @return \Spryker\Zed\Availability\Dependency\Facade\AvailabilityToProductFacadeInterface
     */
    public function getProductFacade(): AvailabilityToProductFacadeInterface
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::FACADE_PRODUCT);
    }

    /**
     * @return array<\Spryker\Zed\AvailabilityExtension\Dependency\Plugin\AvailabilityStrategyPluginInterface>
     */
    public function getAvailabilityStrategyPlugins(): array
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::PLUGINS_AVAILABILITY_STRATEGY);
    }

    /**
     * @return array<\Spryker\Zed\AvailabilityExtension\Dependency\Plugin\BatchAvailabilityStrategyPluginInterface>
     */
    public function getBatchAvailabilityStrategyPlugins(): array
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::PLUGINS_BATCH_AVAILABILITY_STRATEGY);
    }

    /**
     * @return array<\Spryker\Zed\AvailabilityExtension\Dependency\Plugin\CartItemQuantityCounterStrategyPluginInterface>
     */
    public function getCartItemQuantityCounterStrategyPlugins(): array
    {
        return $this->getProvidedDependency(AvailabilityDependencyProvider::PLUGINS_CART_ITEM_QUANTITY_COUNTER_STRATEGY);
    }
}
