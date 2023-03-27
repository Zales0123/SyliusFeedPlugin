<?php

declare(strict_types=1);

namespace Setono\SyliusFeedPlugin\Controller\Action\Admin;

use Setono\SyliusFeedPlugin\Message\Command\ProcessFeed;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ProcessFeedAction
{
    private MessageBusInterface $commandBus;

    private UrlGeneratorInterface $urlGenerator;

    private Request $request;

    private TranslatorInterface $translator;

    public function __construct(
        MessageBusInterface $commandBus,
        UrlGeneratorInterface $urlGenerator,
        Request $request,
        TranslatorInterface $translator
    ) {
        $this->commandBus = $commandBus;
        $this->urlGenerator = $urlGenerator;
        $this->request = $request;
        $this->translator = $translator;
    }

    public function __invoke(int $id): RedirectResponse
    {
        $this->commandBus->dispatch(new ProcessFeed($id));

        $session = $this->request->getSession();
        $session->getFlashBag()->add('success', $this->translator->trans('setono_sylius_feed.feed_generation_triggered'));

        return new RedirectResponse($this->urlGenerator->generate('setono_sylius_feed_admin_feed_show', ['id' => $id]));
    }
}
