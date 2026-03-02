<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CustomerAccessRestApi\Processor\CustomerAccess;

use Generated\Shared\Transfer\ContentTypeAccessTransfer;
use Spryker\Glue\CustomerAccessRestApi\CustomerAccessRestApiConfig;
use Spryker\Glue\CustomerAccessRestApi\Dependency\Client\CustomerAccessRestApiToCustomerAccessStorageClientInterface;
use Spryker\Glue\GlueApplication\Rest\Request\RequestBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

class CustomerAccessRequestFormatter implements CustomerAccessRequestFormatterInterface
{
    /**
     * @uses \Spryker\Glue\GlueApplication\Rest\RequestConstantsInterface::ATTRIBUTE_IS_PROTECTED
     *
     * @var string
     */
    protected const REQUEST_ATTRIBUTE_IS_PROTECTED = 'is-protected';

    /**
     * @var string
     */
    protected const REQUEST_ATTRIBUTE_TYPE = 'type';

    /**
     * @var \Spryker\Glue\CustomerAccessRestApi\Dependency\Client\CustomerAccessRestApiToCustomerAccessStorageClientInterface
     */
    protected $customerAccessStorageClient;

    /**
     * @var \Spryker\Glue\CustomerAccessRestApi\CustomerAccessRestApiConfig
     */
    protected $customerAccessRestApiConfig;

    public function __construct(
        CustomerAccessRestApiToCustomerAccessStorageClientInterface $customerAccessStorageClient,
        CustomerAccessRestApiConfig $customerAccessRestApiConfig
    ) {
        $this->customerAccessStorageClient = $customerAccessStorageClient;
        $this->customerAccessRestApiConfig = $customerAccessRestApiConfig;
    }

    public function updateResourceIsProtectedFlag(RequestBuilderInterface $requestBuilder, Request $request): RequestBuilderInterface
    {
        $currentResourceCustomerAccessContentType = $this->getCurrentResourceCustomerAccessContentType($request);
        if (!$currentResourceCustomerAccessContentType) {
            return $requestBuilder;
        }

        $customerAccessTransfer = $this->customerAccessStorageClient->getAuthenticatedCustomerAccess();

        $request->attributes->set(static::REQUEST_ATTRIBUTE_IS_PROTECTED, false);

        foreach ($customerAccessTransfer->getContentTypeAccess() as $contentTypeAccessTransfer) {
            if ($this->isCustomerAccessContentTypeRestricted($contentTypeAccessTransfer, $currentResourceCustomerAccessContentType)) {
                $request->attributes->set(static::REQUEST_ATTRIBUTE_IS_PROTECTED, true);

                return $requestBuilder;
            }
        }

        return $requestBuilder;
    }

    protected function getCurrentResourceCustomerAccessContentType(Request $request): ?string
    {
        $customerAccessContentTypeToResourceTypeMapping = $this->customerAccessRestApiConfig
            ->getCustomerAccessContentTypeToResourceTypeMapping();
        $currentResourceType = $request->attributes->get(static::REQUEST_ATTRIBUTE_TYPE);

        foreach ($customerAccessContentTypeToResourceTypeMapping as $customerAccessContentType => $resourceTypes) {
            if (in_array($currentResourceType, $resourceTypes)) {
                return $customerAccessContentType;
            }
        }

        return null;
    }

    protected function isCustomerAccessContentTypeRestricted(
        ContentTypeAccessTransfer $contentTypeAccessTransfer,
        string $currentResourceCustomerAccessContentType
    ): bool {
        return $contentTypeAccessTransfer->getContentType() === $currentResourceCustomerAccessContentType
            && $contentTypeAccessTransfer->getIsRestricted();
    }
}
