<?php

declare(strict_types=1);

namespace App\Shared\Bus;

interface QueryBus
{
    public function dispatch(object $query): mixed;
}
