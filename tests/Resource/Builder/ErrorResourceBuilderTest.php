<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Tests\Resource\Builder;

use Exception;
use PHPUnit\Framework\TestCase;
use Saikootau\ApiBundle\Resource\Builder\ErrorResourceBuilder;
use Saikootau\ApiBundle\Resource\Error;

class ErrorResourceBuilderTest extends TestCase
{
    public function testErrorIsCreated(): void
    {
        $builder = new ErrorResourceBuilder();

        $exception = new Exception('test', 123);

        $errors = $builder->build($exception);

        $this->assertCount(1, $errors);
        $this->assertContainsOnly(Error::class, $errors);
        $this->assertSame(Exception::class, $errors[0]->getType());
        $this->assertSame('test', $errors[0]->getMessage());
        $this->assertSame('123', $errors[0]->getCode());
    }

    public function testErrorStackIsCreated(): void
    {
        $builder = new ErrorResourceBuilder();

        $exception1 = new Exception('test1', 123);
        $exception2 = new Exception('test2', 234, $exception1);
        $exception3 = new Exception('test3', 345, $exception2);

        $errors = $builder->traceStack()->build($exception3);

        $this->assertCount(3, $errors);
        $this->assertContainsOnly(Error::class, $errors);
        $this->assertSame('test3', $errors[0]->getMessage());
        $this->assertSame('test2', $errors[1]->getMessage());
        $this->assertSame('test1', $errors[2]->getMessage());
    }

    public function testStackIsOnlyCreatedIfEnabled(): void
    {
        $builder = new ErrorResourceBuilder();

        $exception1 = new Exception('test1', 123);
        $exception2 = new Exception('test2', 234, $exception1);
        $exception3 = new Exception('test3', 345, $exception2);

        $errors = $builder->doNotTraceStack()->build($exception3);

        $this->assertCount(1, $errors);
        $this->assertContainsOnly(Error::class, $errors);
        $this->assertSame('test3', $errors[0]->getMessage());
    }
}
