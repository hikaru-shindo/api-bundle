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

        if (!$this->isContentAllowed($event->getRequest()->getMethod())) {
            $event->setResponse(
                $this->createEmptyResponse(Response::HTTP_OK)
            );

            return;
        }

        $serializer = $this->getSerializer($event->getRequest());
        $event->setResponse(new Response(
            $serializer->serialize($controllerResult),
            Response::HTTP_OK,
            ['Content-Type' => $serializer->getContentType()]
        ));
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
     * Create a response without any body.
     *
     * @param int $status
     *
     * @return Response
     */
    private function createEmptyResponse(int $status): Response
    {
        return new Response(
            '', $status
        );
    }
}
