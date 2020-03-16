<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EventSubscriber implements EventSubscriberInterface
{
    /**
     * @param RequestEvent $event
     * @return void
     */
    public function onConvert(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (false === $this->isAvailable($request)) {
            return;
        }

        if (false === $this->transform($request)) {
            $request = Response::create('Unable to parse request.', 400);

            $event->setResponse($request);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onConvert', 10],
        ];
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function isAvailable(Request $request): bool
    {
        return 'json' === $request->getContentType() && $request->getContent();
    }

    /**
     * @param Request $request
     * @return bool
     */
    private function transform(Request $request): bool
    {
        $data = json_decode($request->getContent(), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        if (is_array($data)) {
            $request->request->replace($data);
        }

        return true;
    }
}
