<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Availability\Business\Expander\Wishlist;

use Generated\Shared\Transfer\WishlistItemTransfer;

interface AvailabilityWishlistItemExpanderInterface
{
    public function expandWishlistItemWithAvailability(WishlistItemTransfer $wishlistItemTransfer): WishlistItemTransfer;

    public function expandWishlistItemWithSellable(WishlistItemTransfer $wishlistItemTransfer): WishlistItemTransfer;
}
