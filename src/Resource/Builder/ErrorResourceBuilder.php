<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Resource\Builder;

use Saikootau\ApiBundle\Resource\Error;
use Throwable;
use ReflectionClass;
use ReflectionException;

class ErrorResourceBuilder
{
    private $traceStack = true;

    /**
     * Trace the whole exception stack.
     *
     * @return ErrorResourceBuilder
     */
    public function traceStack(): self
    {
        $this->traceStack = true;

        return $this;
    }

    /**
     * Only evaluate the topmost exception in stack.
     *
     * @return ErrorResourceBuilder
     */
    public function doNotTraceStack(): self
    {
        $this->traceStack = false;

        return $this;
    }

    /**
     * Build an error resource from an exception.
     *
     * @param Throwable $exception
     *
     * @throws ReflectionException
     *
     * @return Error[]
     */
    public function build(Throwable $exception): array
    {
        $errors = [];

        $errors[] = $this->createError($exception);
        if ($this->traceStack && $exception->getPrevious()) {
            $errors = array_merge($errors, $this->build($exception->getPrevious()));
        }

        return $errors;
    }

    /**
     * Create an error from an exception.
     *
     * @param Throwable $exception
     *
     * @throws ReflectionException
     *
     * @return Error
     */
    private function createError(Throwable $exception): Error
    {
        $reflection = new ReflectionClass($exception);

        return new Error($reflection->getShortName(), $exception->getMessage(), (string) $exception->getCode());
    }
}
