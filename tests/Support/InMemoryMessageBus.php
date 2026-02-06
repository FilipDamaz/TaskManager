<?php

namespace App\Tests\Support;

use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class InMemoryMessageBus implements MessageBusInterface
{
    /**
     * @var object[]
     */
    public array $messages = [];

    public function dispatch(object $message, array $stamps = []): Envelope
    {
        $this->messages[] = $message;

        return new Envelope($message);
    }
}
