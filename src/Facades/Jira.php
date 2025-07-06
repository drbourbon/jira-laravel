<?php

declare(strict_types=1);

namespace Jira\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Jira\Client;

/**
 * @see Client
 */
class Jira extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'jira';
    }
}
