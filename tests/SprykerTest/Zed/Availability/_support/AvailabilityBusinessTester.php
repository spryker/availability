<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\Availability;

use Codeception\Actor;
use Generated\Shared\Transfer\ProductAbstractAvailabilityTransfer;
use Generated\Shared\Transfer\ProductAbstractTransfer;
use Generated\Shared\Transfer\ProductAvailabilityDataTransfer;
use Generated\Shared\Transfer\ProductConcreteAvailabilityTransfer;
use Generated\Shared\Transfer\ProductConcreteTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Orm\Zed\Availability\Persistence\SpyAvailability;
use Orm\Zed\Availability\Persistence\SpyAvailabilityAbstractQuery;
use Orm\Zed\Availability\Persistence\SpyAvailabilityQuery;

/**
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Spryker\Zed\Availability\Business\AvailabilityFacadeInterface getFacade()
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = null)
 *
 * @SuppressWarnings(\SprykerTest\Zed\Availability\PHPMD)
 */
class AvailabilityBusinessTester extends Actor
{
    use _generated\AvailabilityBusinessTesterActions;

    public function findAvailabilityConcreteForStore(string $productSku, int $idStore): ?SpyAvailability
    {
        return $this->getAvailabilityQuery()
            ->filterBySku($productSku)
            ->filterByFkStore($idStore)
            ->findOne();
    }

    public function getAvailabilityQuery(): SpyAvailabilityQuery
    {
        return SpyAvailabilityQuery::create();
    }

    public function getAvailabilityAbstractQuery(): SpyAvailabilityAbstractQuery
    {
        return SpyAvailabilityAbstractQuery::create();
    }

    public function prepareProductAvailabilityDataTransfer(
        ProductConcreteTransfer $productConcreteTransfer,
        StoreTransfer $storeTransfer
    ): ProductAvailabilityDataTransfer {
        $productAvailabilityDataTransfer = new ProductAvailabilityDataTransfer();
        $productAvailabilityDataTransfer->addProductAbstractAvailability(
            (new ProductAbstractAvailabilityTransfer())
                ->setAvailability(15)
                ->setSku($productConcreteTransfer->getAbstractSku())
                ->setIdStore($storeTransfer->getIdStore()),
        );
        $productAvailabilityDataTransfer->addProductConcreteAvailability(
            (new ProductConcreteAvailabilityTransfer())
                ->setAvailability(10)
                ->setSku($productConcreteTransfer->getSku())
                ->setStore($storeTransfer),
        );

        return $productAvailabilityDataTransfer
            ->setProductAbstract((new ProductAbstractTransfer())
                ->setSku($productConcreteTransfer->getAbstractSku())
                ->setIdProductAbstract($productConcreteTransfer->getFkProductAbstract()))
            ->setProductConcrete($productConcreteTransfer);
    }
}
