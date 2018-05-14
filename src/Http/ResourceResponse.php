<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Http;

use Symfony\Component\HttpFoundation\Response;

class ResourceResponse
{
    private $resource;
    private $statusCode;
    private $headers;

    public function __construct(object $resource, int $statusCode = Response::HTTP_OK, array $headers = [])
    {
        $this->resource = $resource;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    /**
     * Returns the resource to be send in the response.
     *
     * @return object
     */
    public function getResource(): object
    {
        return $this->resource;
    }

    /**
     * Returns the status code to be returned for the request given.
     *
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Returns any additional headers to be send for the request.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
