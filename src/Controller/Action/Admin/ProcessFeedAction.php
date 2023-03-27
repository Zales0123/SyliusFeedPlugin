<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Controller\Action\Admin;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Setono\SyliusFeedPlugin\Message\Command\ProcessFeed;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

final class ProcessFeedAction
{
    private MessageBusInterface $commandBus;

    private UrlGeneratorInterface $urlGenerator;

    private RequestStack $requestStack;

    private TranslatorInterface $translator;

    public function __construct(
        MessageBusInterface $commandBus,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack,
        TranslatorInterface $translator
    ) {
        $this->commandBus = $commandBus;
        $this->urlGenerator = $urlGenerator;
        $this->requestStack = $requestStack;
        $this->translator = $translator;
    }

    public function __invoke(int $id): RedirectResponse
    {
        $this->commandBus->dispatch(new ProcessFeed($id));

        $session = $this->requestStack->getCurrentRequest()->getSession();
        /** @var FlashBagInterface $flashBag */
        $flashBag = $session->getBag('flashes');
        $flashBag->add('success', $this->translator->trans('setono_sylius_feed.feed_generation_triggered'));

        return new RedirectResponse($this->urlGenerator->generate('setono_sylius_feed_admin_feed_show', ['id' => $id]));
    }
}
