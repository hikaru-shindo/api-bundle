<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Resource\Builder;

use Saikootau\ApiBundle\Exception\ExposableError;
use Saikootau\ApiBundle\Resource\Error;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Throwable;
use ReflectionClass;
use ReflectionException;

class ErrorResourceBuilder
{
    private $exposeStack = true;

    /**
     * Trace the whole exception stack.
     *
     * @return ErrorResourceBuilder
     */
    public function exposeStack(): self
    {
        $this->exposeStack = true;

        return $this;
    }

    /**
     * Only evaluate the topmost exception in stack.
     *
     * @return ErrorResourceBuilder
     */
    public function doNotExposeStack(): self
    {
        $this->exposeStack = false;

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

        if ($this->shouldBeExposed($exception)) {
            $errors[] = $this->createError($exception);
        }

        if ($exception->getPrevious()) {
            $errors = array_merge($errors, $this->build($exception->getPrevious()));
        }

        return $errors;
    }

    private function shouldBeExposed(Throwable $exception): bool
    {
        if ($this->exposeStack) {
            return true;
        }

        return $exception instanceof HttpExceptionInterface
            || $exception instanceof ExposableError
        ;
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
        return new Error($this->getErrorName($exception), $exception->getMessage(), (string) $exception->getCode());
    }

    /**
     * Get name for given exception to show in stack.
     *
     * @param Throwable $exception
     *
     * @throws ReflectionException
     *
     * @return string
     */
    private function getErrorName(Throwable $exception): string
    {
        if ($exception instanceof ExposableError) {
            return $exception->getShowName();
        }

        $reflection = new ReflectionClass($exception);

        return $reflection->getShortName();
    }
}
