<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Resource\Builder;

use Saikootau\ApiBundle\Resource\Error;
use Saikootau\ApiBundle\Resource\Service;
use Symfony\Component\HttpFoundation\Request;

class ServiceResourceBuilder
{
    private $request;
    private $errors = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Add errors to the stack.
     *
     * @param Error ...$errors
     *
     * @return ServiceResourceBuilder
     */
    public function addError(Error ...$errors): self
    {
        $this->errors = array_merge($this->errors, $errors);

        return $this;
    }

    public function build(): Service
    {
        $requestResource = (new RequestResourceBuilder())->build($this->request);

        return new Service($requestResource, ...$this->errors);
    }
}
