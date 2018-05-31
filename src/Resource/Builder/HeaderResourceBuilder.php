<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Resource\Builder;

use Saikootau\ApiBundle\Resource\Header;
use Symfony\Component\HttpFoundation\Request;

class HeaderResourceBuilder
{
    /**
     * Build a collection of header resources from a given request.
     *
     * @param Request $request
     *
     * @return Header[]
     */
    public function build(Request $request): array
    {
        $headers = [];

        foreach ($request->headers as $name => $values) {
            foreach ($values as $value) {
                $headers[] = new Header((string) $name, (string) $value);
            }
        }

        return $headers;
    }
}
