<?php

declare(strict_types=1);

namespace App\Shared\Bus;

interface CommandBus
{
    public function dispatch(object $command): mixed;
}
