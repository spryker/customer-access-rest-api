<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CustomerAccessRestApi;

use Spryker\Glue\CustomerAccessRestApi\Dependency\Client\CustomerAccessRestApiToCustomerAccessStorageClientBridge;
use Spryker\Glue\Kernel\AbstractBundleDependencyProvider;
use Spryker\Glue\Kernel\Container;

/**
 * @method \Spryker\Glue\CustomerAccessRestApi\CustomerAccessRestApiConfig getConfig()
 */
class CustomerAccessRestApiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const CLIENT_CUSTOMER_ACCESS_STORAGE = 'CLIENT_CUSTOMER_ACCESS_STORAGE';

    public function provideDependencies(Container $container): Container
    {
        $container = parent::provideDependencies($container);
        $container = $this->addCustomerAccessStorageClient($container);

        return $container;
    }

    protected function addCustomerAccessStorageClient(Container $container): Container
    {
        $container->set(static::CLIENT_CUSTOMER_ACCESS_STORAGE, function (Container $container) {
            return new CustomerAccessRestApiToCustomerAccessStorageClientBridge(
                $container->getLocator()->customerAccessStorage()->client(),
            );
        });

        return $container;
    }
}
