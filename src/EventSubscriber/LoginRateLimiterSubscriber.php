<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;


class LoginRateLimiterSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private RateLimiterFactory $loginIpLimiter,
        private RateLimiterFactory $loginEmailLimiter,
        private RequestStack $requestStack,
        private UrlGeneratorInterface $urlGenerator,
        private LoggerInterface $logger
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
            LoginFailureEvent::class => 'onLoginFailure',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        
        if ($request->attributes->get('_route') !== 'app_login' || !$request->isMethod('POST')) {
            return;
        }

        $ip = $request->getClientIp();
        $email = $request->request->get('email', '');

        $ipLimiter = $this->loginIpLimiter->create($ip);
        if (!$ipLimiter->consume(1)->isAccepted()) {
            $this->logger->warning('Rate limit exceeded for IP', [
                'ip' => $ip,
                'email' => $email,
            ]);

            $session = $this->requestStack->getSession();
            $session->getFlashBag()->add(
                'error',
                'Trop de tentatives de connexion depuis cette adresse IP. Veuillez réessayer dans 15 minutes.'
            );

            $response = new RedirectResponse($this->urlGenerator->generate('app_login'));
            $event->setResponse($response);
            return;
        }

        if ($email) {
            $emailLimiter = $this->loginEmailLimiter->create($email);
            if (!$emailLimiter->consume(1)->isAccepted()) {
                $this->logger->warning('Rate limit exceeded for email', [
                    'ip' => $ip,
                    'email' => $email,
                ]);

                $session = $this->requestStack->getSession();
                $session->getFlashBag()->add(
                    'error',
                    'Trop de tentatives de connexion pour cet email. Veuillez réessayer dans 5 minutes.'
                );

                $response = new RedirectResponse($this->urlGenerator->generate('app_login'));
                $event->setResponse($response);
                return;
            }
        }
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $request = $event->getRequest();
        $email = $request->request->get('email', '');
        $ip = $request->getClientIp();

        $this->logger->info('Login failure', [
            'email' => $email,
            'ip' => $ip,
            'exception' => $event->getException()?->getMessage(),
        ]);
    }
}
