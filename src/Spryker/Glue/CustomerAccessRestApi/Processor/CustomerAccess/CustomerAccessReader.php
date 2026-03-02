<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CustomerAccessRestApi\Processor\CustomerAccess;

use Generated\Shared\Transfer\ContentTypeAccessTransfer;
use Spryker\Glue\CustomerAccessRestApi\CustomerAccessRestApiConfig;
use Spryker\Glue\CustomerAccessRestApi\Dependency\Client\CustomerAccessRestApiToCustomerAccessStorageClientInterface;
use Spryker\Glue\CustomerAccessRestApi\Processor\RestResponseBuilder\CustomerAccessRestResponseBuilderInterface;
use Spryker\Glue\GlueApplication\Rest\JsonApi\RestResponseInterface;
use Spryker\Glue\GlueApplication\Rest\Request\Data\RestRequestInterface;

class CustomerAccessReader implements CustomerAccessReaderInterface
{
    /**
     * @var \Spryker\Glue\CustomerAccessRestApi\CustomerAccessRestApiConfig
     */
    protected $customerAccessRestApiConfig;

    /**
     * @var \Spryker\Glue\CustomerAccessRestApi\Dependency\Client\CustomerAccessRestApiToCustomerAccessStorageClientInterface
     */
    protected $customerAccessStorageClient;

    /**
     * @var \Spryker\Glue\CustomerAccessRestApi\Processor\RestResponseBuilder\CustomerAccessRestResponseBuilderInterface
     */
    protected $customerAccessRestResponseBuilder;

    public function __construct(
        CustomerAccessRestApiConfig $customerAccessRestApiConfig,
        CustomerAccessRestApiToCustomerAccessStorageClientInterface $customerAccessStorageClient,
        CustomerAccessRestResponseBuilderInterface $customerAccessRestResponseBuilder
    ) {
        $this->customerAccessRestApiConfig = $customerAccessRestApiConfig;
        $this->customerAccessStorageClient = $customerAccessStorageClient;
        $this->customerAccessRestResponseBuilder = $customerAccessRestResponseBuilder;
    }

    public function getCustomerAccess(RestRequestInterface $restRequest): RestResponseInterface
    {
        $customerAccessContentTypeToResourceTypeMapping = $this->customerAccessRestApiConfig->getCustomerAccessContentTypeToResourceTypeMapping();
        $customerAccessContentTypeToResourceTypeMapping = $this->filterOutUnrestrictedCustomerAccessContentTypes($customerAccessContentTypeToResourceTypeMapping);

        return $this->customerAccessRestResponseBuilder
            ->createCustomerAccessResponse($customerAccessContentTypeToResourceTypeMapping);
    }

    protected function filterOutUnrestrictedCustomerAccessContentTypes(array $customerAccessContentTypeResourceType): array
    {
        $authenticatedCustomerAccessTransfer = $this->customerAccessStorageClient->getAuthenticatedCustomerAccess();
        foreach ($authenticatedCustomerAccessTransfer->getContentTypeAccess() as $contentTypeAccessTransfer) {
            if ($this->isCustomerAccessContentTypeUnrestricted($contentTypeAccessTransfer, $customerAccessContentTypeResourceType)) {
                unset($customerAccessContentTypeResourceType[$contentTypeAccessTransfer->getContentType()]);
            }
        }

        return $customerAccessContentTypeResourceType;
    }

    protected function isCustomerAccessContentTypeUnrestricted(
        ContentTypeAccessTransfer $contentTypeAccessTransfer,
        array $customerAccessContentTypeResourceType
    ): bool {
        return !array_key_exists($contentTypeAccessTransfer->getContentType(), $customerAccessContentTypeResourceType) || !$contentTypeAccessTransfer->getIsRestricted();
    }
}
