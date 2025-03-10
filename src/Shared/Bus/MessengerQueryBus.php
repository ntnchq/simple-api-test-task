<?php

declare(strict_types=1);

namespace App\Shared\Bus;

use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class MessengerQueryBus implements QueryBus
{
    public function __construct(
        private readonly MessageBusInterface $queryBus,
    ) {
    }

    public function dispatch(object $query): mixed
    {
        try {
            $envelope = $this->queryBus->dispatch($query);
            /** @var HandledStamp $stamp */
            $stamp = $envelope->last(HandledStamp::class);

            return $stamp->getResult();
        } catch (HandlerFailedException $e) {
            throw $e->getPrevious() ?? $e;
        }
    }
}
