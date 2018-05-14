<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Resource\Builder;

use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Saikootau\ApiBundle\Resource\Request;

class RequestResourceBuilder
{
    /**
     * Builds a request resource from a given request.
     *
     * @param HttpRequest $request
     *
     * @return Request
     */
    public function build(HttpRequest $request): Request
    {
        $headers = (new HeaderResourceBuilder())->build($request);

        return new Request($request->getMethod(), $request->getUri(), ...$headers);
    }
}
