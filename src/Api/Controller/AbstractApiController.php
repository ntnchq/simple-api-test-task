<?php

declare(strict_types=1);

namespace App\Api\Controller;

use App\Shared\Bus\CommandBus;
use App\Shared\Bus\QueryBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractApiController extends AbstractController
{
    public function __construct(
        protected readonly CommandBus $commandBus,
        protected readonly QueryBus $queryBus,
    ) {
    }
}
