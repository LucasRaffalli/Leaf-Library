<?php

namespace App\EventSubscriber;

use App\Exception\BookNotAvailableException;
use App\Exception\BorrowLimitExceededException;
use App\Exception\UserNotActiveException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator,
        private LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if ($exception instanceof BookNotAvailableException ||
            $exception instanceof UserNotActiveException ||
            $exception instanceof BorrowLimitExceededException) {
            
            $this->logger->warning('Business exception occurred', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
                'user' => $request->getSession()->get('_security.last_username'),
                'ip' => $request->getClientIp(),
            ]);

            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add('error', $exception->getMessage());

            $response = new RedirectResponse($this->urlGenerator->generate('app_catalogue'));
            $event->setResponse($response);
        }
    }
}
