<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;
use Generated\Shared\Transfer\ProductAvailabilityCriteriaTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityCollectionTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityRequestTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SellableItemsRequestTransfer;
use Generated\Shared\Transfer\SellableItemsResponseTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Generated\Shared\Transfer\WishlistItemTransfer;
use Spryker\DecimalObject\Decimal;

interface AvailabilityFacadeInterface
{
    /**
     * Specification:
     *  - Checks if product is never out of stock for given store.
     *  - Checks if product has stock in stock table.
     *  - Checks if have placed orders where items have state machine state flagged as reserved.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\SellableItemsRequestTransfer $sellableItemsRequestTransfer
     *
     * @return \Generated\Shared\Transfer\SellableItemsResponseTransfer
     */
    public function areProductsSellableForStore(
        SellableItemsRequestTransfer $sellableItemsRequestTransfer
    ): SellableItemsResponseTransfer;

    /**
     * Specification:
     *  - Checks if product is never out of stock for given store.
     *  - Checks if product has stock in stock table.
     *  - Checks if have placed orders where items have state machine state flagged as reserved.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\SellableItemsRequestTransfer $sellableItemsRequestTransfer
     * @param \Generated\Shared\Transfer\SellableItemsResponseTransfer $sellableItemsResponseTransfer
     *
     * @return \Generated\Shared\Transfer\SellableItemsResponseTransfer
     */
    public function areProductConcretesSellableForStore(
        SellableItemsRequestTransfer $sellableItemsRequestTransfer,
        SellableItemsResponseTransfer $sellableItemsResponseTransfer
    ): SellableItemsResponseTransfer;

    /**
     * Specification:
     *  - Checks if product is never out of stock for given store.
     *  - Checks if product has stock in stock table.
     *  - Checks if have placed orders where items have state machine state flagged as reserved.
     *
     * @api
     *
     * @deprecated Use {@link Spryker\Zed\Availability\Business\AvailabilityFacadeInterface::areProductsSellableForStore()} instead.
     *
     * @param string $sku
     * @param \Spryker\DecimalObject\Decimal $quantity
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param \Generated\Shared\Transfer\ProductAvailabilityCriteriaTransfer|null $productAvailabilityCriteriaTransfer
     *
     * @return bool
     */
    public function isProductSellableForStore(
        string $sku,
        Decimal $quantity,
        StoreTransfer $storeTransfer,
        ?ProductAvailabilityCriteriaTransfer $productAvailabilityCriteriaTransfer = null
    ): bool;

    /**
     * Specification:
     *  - Checks if product is available.
     *
     * @api
     *
     * @deprecated Use {@link Spryker\Zed\Availability\Business\AvailabilityFacadeInterface::areProductConcretesSellableForStore()} instead.
     *
     * @param int $idProductConcrete
     *
     * @return bool
     */
    public function isProductConcreteAvailable(int $idProductConcrete): bool;

    /**
     * Specification:
     *  - Returns calculated availability value which is product stock minus reserved state machine items quantities.
     *
     * @api
     *
     * @param string $sku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Spryker\DecimalObject\Decimal
     */
    public function calculateAvailabilityForProductWithStore(string $sku, StoreTransfer $storeTransfer): Decimal;

    /**
     * Specification:
     * - Checks if all items in the cart are sellable.
     * - Executes {@link \Spryker\Zed\AvailabilityExtension\Dependency\Plugin\AvailabilityStrategyPluginInterface} plugin stack.
     * - Executes {@link \Spryker\Zed\AvailabilityExtension\Dependency\Plugin\BatchAvailabilityStrategyPluginInterface} plugin stack.
     * - If plugins are not provided, executes default batch strategy.
     * - In case `ItemTransfer.amount` was defined, item availability check will be ignored.
     * - Writes error message into CheckoutResponseTransfer.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponseTransfer
     *
     * @return bool
     */
    public function checkoutAvailabilityPreCondition(
        QuoteTransfer $quoteTransfer,
        CheckoutResponseTransfer $checkoutResponseTransfer
    );

    /**
     * Specification:
     *  - Calculates current item availability, take into account reserved items.
     *  - Updates availability for stores where product stock and/or availability are defined.
     *  - Stores new availability for concrete product.
     *  - Stores sum of all concrete product availability for abstract product.
     *  - Touches availability abstract collector if data changed.
     *
     * @api
     *
     * @param string $sku
     *
     * @return void
     */
    public function updateAvailability($sku);

    /**
     * Specification:
     *  - Calculates current item availability, for given store take into account reserved items
     *  - Stores availability for concrete product
     *  - Stores sum of all concrete product availability for abstract product
     *  - Touches availability abstract collector if data changed
     *
     * @api
     *
     * @param string $sku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return void
     */
    public function updateAvailabilityForStore($sku, StoreTransfer $storeTransfer);

    /**
     * Specification:
     *  - Reads product availability data from persistence, stock, reservation, availability.
     *  - Returns data for selected abstract product.
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\Availability\Business\AvailabilityFacadeInterface::findOrCreateProductAbstractAvailabilityBySkuForStore()} instead.
     *
     * @param int $idProductAbstract
     * @param int $idLocale
     *
     * @return \Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer
     */
    public function getProductAbstractAvailability($idProductAbstract, $idLocale);

    /**
     * Specification:
     *  - Reads product availability data from persistence, stock, reservation, availability.
     *  - Returns data for selected abstract product.
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\Availability\Business\AvailabilityFacadeInterface::findOrCreateProductAbstractAvailabilityBySkuForStore()} instead.
     *
     * @param int $idProductAbstract
     * @param int $idLocale
     * @param int $idStore
     *
     * @return \Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer|null
     */
    public function findProductAbstractAvailability($idProductAbstract, $idLocale, $idStore);

    /**
     * Specification:
     *  - Finds product concrete availability as is stored in persistence.
     *
     * @api
     *
     * @deprecated Use {@link \Spryker\Zed\Availability\Business\AvailabilityFacadeInterface::findOrCreateProductConcreteAvailabilityBySkuForStore()} instead.
     *
     * @param \Generated\Shared\Transfer\ProductConcreteAvailabilityRequestTransfer $productConcreteAvailabilityRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer|null
     */
    public function findProductConcreteAvailability(ProductConcreteAvailabilityRequestTransfer $productConcreteAvailabilityRequestTransfer);

    /**
     * Specification:
     *  - Finds product abstract availability as is stored in persistence.
     *  - If nothing was stored in persistence, abstract availability will be calculated and stored.
     *
     * @api
     *
     * @param string $sku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return \Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer|null
     */
    public function findOrCreateProductAbstractAvailabilityBySkuForStore(string $sku, StoreTransfer $storeTransfer): ?ProductAbstractAvailabilityTransfer;

    /**
     * Specification:
     *  - Finds product concrete availability as is stored in persistence.
     *  - If nothing was stored in persistence, concrete availability will be calculated and stored.
     *
     * @api
     *
     * @param string $sku
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     * @param \Generated\Shared\Transfer\ProductAvailabilityCriteriaTransfer|null $productAvailabilityCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer|null
     */
    public function findOrCreateProductConcreteAvailabilityBySkuForStore(
        string $sku,
        StoreTransfer $storeTransfer,
        ?ProductAvailabilityCriteriaTransfer $productAvailabilityCriteriaTransfer = null
    ): ?ProductConcreteAvailabilityTransfer;

    /**
     * Specification:
     *  - Touches availability abstract collector for given abstract product
     *
     * @api
     *
     * @param int $idAvailabilityAbstract
     *
     * @return void
     */
    public function touchAvailabilityAbstract($idAvailabilityAbstract);

    /**
     * Specification:
     *  - Updates availability for given concrete sku, by quantity.
     *  - Touches availability collector if data changed
     *  - Returns id of availability abstract
     *
     * @api
     *
     * @param string $sku
     * @param \Spryker\DecimalObject\Decimal $quantity
     * @param \Generated\Shared\Transfer\StoreTransfer $storeTransfer
     *
     * @return int
     */
    public function saveProductAvailabilityForStore(string $sku, Decimal $quantity, StoreTransfer $storeTransfer): int;

    /**
     * Specification:
     *  - Returns all stores where availability of product with given sku is defined.
     *
     * @api
     *
     * @param string $concreteSku
     *
     * @return array<\Generated\Shared\Transfer\StoreTransfer>
     */
    public function getStoresWhereProductAvailabilityIsDefined(string $concreteSku): array;

    /**
     * Specification:
     * - Filters out products which are not available and returns back modified array.
     * - Requires ProductConcreteTransfer::idProductConcrete to be set.
     *
     * @api
     *
     * @param array<\Generated\Shared\Transfer\ProductConcreteTransfer> $productConcreteTransfers
     *
     * @return array<\Generated\Shared\Transfer\ProductConcreteTransfer>
     */
    public function filterAvailableProducts(array $productConcreteTransfers): array;

    /**
     * Specification:
     * - Reads product availabilities from DB.
     * - Returns ProductConcreteAvailability transfers collection by provided criteria.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductAvailabilityCriteriaTransfer $productAvailabilityCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductConcreteAvailabilityCollectionTransfer
     */
    public function getProductConcreteAvailabilityCollection(
        ProductAvailabilityCriteriaTransfer $productAvailabilityCriteriaTransfer
    ): ProductConcreteAvailabilityCollectionTransfer;

    /**
     * Specification:
     * - Expands `WishlistItem` transfer object with availability data.
     * - Returns expanded `WishlistItem` transfer object.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\WishlistItemTransfer $wishlistItemTransfer
     *
     * @return \Generated\Shared\Transfer\WishlistItemTransfer
     */
    public function expandWishlistItemWithAvailability(WishlistItemTransfer $wishlistItemTransfer): WishlistItemTransfer;

    /**
     * Specification:
     * - Expands `WishlistItem` transfer object with sellable data.
     * - Returns expanded `WishlistItem` transfer object.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\WishlistItemTransfer $wishlistItemTransfer
     *
     * @return \Generated\Shared\Transfer\WishlistItemTransfer
     */
    public function expandWishlistItemWithSellable(WishlistItemTransfer $wishlistItemTransfer): WishlistItemTransfer;
}
