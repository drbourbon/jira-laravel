<?php

declare(strict_types=1);

namespace Jira\Laravel;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Jira;
use Jira\Client;
use Jira\Laravel\Exceptions\ConfigIncomplete;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authenticate;
use App\Http\Middleware\BasicAuthenticateJiraMiddleware;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

/**
 * @internal
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     *
     * @codeCoverageIgnore
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, static function (): Client {

            $host = config('jira.host');
            $user = Auth::user();
            $apiKey = $user->jira_token;

            if (! is_string($apiKey) || ! is_string($host)) {
                throw ConfigIncomplete::create();
            }

            return Jira::client($apiKey, $host);
        });

        $this->app->alias(Client::class, 'jira');
    }

    /**
     * Bootstrap any application services.
     *
     * @codeCoverageIgnore
     */
    public function boot(Router $router): void
    {
        $this->publishes([
            __DIR__.'/../config/jira.php' => config_path('jira.php'),
        ]);

        $router->middlewareGroup('web', [
            Authenticate::class
        ]);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [
            Client::class,
        ];
    }
}
