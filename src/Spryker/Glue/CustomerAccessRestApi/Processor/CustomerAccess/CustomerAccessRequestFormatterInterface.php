<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Glue\CustomerAccessRestApi\Processor\CustomerAccess;

use Spryker\Glue\GlueApplication\Rest\Request\RequestBuilderInterface;
use Symfony\Component\HttpFoundation\Request;

interface CustomerAccessRequestFormatterInterface
{
    public function updateResourceIsProtectedFlag(RequestBuilderInterface $requestBuilder, Request $request): RequestBuilderInterface;
}
