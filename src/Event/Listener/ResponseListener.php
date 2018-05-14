<?php

declare(strict_types=1);

namespace Saikootau\ApiBundle\Event\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ResponseListener extends MediaTypeListener
{
    private static $noContentMethods = [
        'HEAD',
    ];

    public function onKernelView(GetResponseForControllerResultEvent $event): void
    {
        $controllerResult = $event->getControllerResult();

        if ($controllerResult instanceof Response) {
            return;
        }

        $serializer = $this->getSerializer($event->getRequest());
        $body = '';
        if ($this->isContentAllowed($event->getRequest()->getMethod())) {
            $body = $serializer->serialize($controllerResult);
        }

        $event->setResponse(
            $this->createResponse(
                $body,
                Response::HTTP_OK,
                $serializer->getContentType()
            )
        );
    }

    /**
     * Checks if the response should contain any content.
     *
     * @param string $method
     *
     * @return bool
     */
    private function isContentAllowed(string $method): bool
    {
        return !in_array($method, self::$noContentMethods);
    }

    /**
     * Create a response for the given data.
     *
     * @param string $body
     * @param int    $status
     * @param string $contentType
     *
     * @return Response
     */
    private function createResponse(string $body, int $status, string $contentType): Response
    {
        return new Response(
            $body,
            $status,
            ['Content-Type' => $contentType]
        );
    }
}
