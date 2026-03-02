<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CustomerAccessRestApi\Processor\Mapper;

use Generated\Shared\Transfer\RestCustomerAccessAttributesTransfer;

class CustomerAccessMapper implements CustomerAccessMapperInterface
{
    public function mapCustomerAccessContentTypeResourceTypeToRestCustomerAccessAttributesTransfer(
        array $customerAccessContentTypeResourceTypes,
        RestCustomerAccessAttributesTransfer $restCustomerAccessAttributesTransfer
    ): RestCustomerAccessAttributesTransfer {
        foreach ($customerAccessContentTypeResourceTypes as $customerAccessContentTypeResourceType) {
            foreach ($customerAccessContentTypeResourceType as $resourceType) {
                $restCustomerAccessAttributesTransfer->addResourceType($resourceType);
            }
        }

        return $restCustomerAccessAttributesTransfer;
    }
}
