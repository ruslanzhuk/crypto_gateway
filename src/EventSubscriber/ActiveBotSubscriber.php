<?php

namespace App\EventSubscriber;

use App\Repository\TelegramBotIntegrationRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ActiveBotSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private TelegramBotIntegrationRepository $botRepo,
        private RouterInterface $router,
        private RequestStack $requestStack
    )
    {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (
            str_starts_with($request->getPathInfo(), '/dashboard/botmanager') ||
            str_starts_with($request->getPathInfo(), '/dashboard/user/show') ||
            in_array($request->getPathInfo(), ['/login', '/logout', '/dashboard'])
        ) {
            return;
        }

        $user = $this->security->getUser();
        if (!$user) {
            return;
        }

        if ($user->getActiveTelegramBot() == null) {
            $this->requestStack->getSession()->getFlashBag()->add(
                'error',
                'Ви не можете користуватися додатком, поки не активуєте Telegram-бота. Увімкніть старого або створіть нового.'
            );

            $event->setResponse(new RedirectResponse($this->router->generate('app_bot_manager')));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [KernelEvents::REQUEST => 'onKernelRequest'];
    }
}