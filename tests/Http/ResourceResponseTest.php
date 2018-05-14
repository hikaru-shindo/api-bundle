<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\NeduaType;

use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\Http\ResourceResponse;
use Symfony\Component\HttpFoundation\Response;

class ResourceResponseTest extends TestCase
{
    /** @test */
    public function objectCanBeInitialized(): void
    {
        $resource = new \stdClass();
        $resourceResponse = new ResourceResponse($resource);

        $this->assertSame(spl_object_hash($resource), spl_object_hash($resourceResponse->getResource()));
        $this->assertSame(Response::HTTP_OK, $resourceResponse->getStatusCode());
        $this->assertEmpty($resourceResponse->getHeaders());
    }

    /** @test */
    public function statusCodeCanBeSet(): void
    {
        $resourceResponse = new ResourceResponse(new \stdClass(), Response::HTTP_CREATED);

        $this->assertSame(Response::HTTP_CREATED, $resourceResponse->getStatusCode());
    }

    /** @test */
    public function headersCanBeSet(): void
    {
        $headers = ['X-Test' => 'Test'];
        $resourceResponse = new ResourceResponse(new \stdClass(), Response::HTTP_OK, $headers);

        $this->assertSame($headers, $resourceResponse->getHeaders());
    }
}
