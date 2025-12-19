<?php

namespace J3ns3n\LaravelLinkly;

use Illuminate\Support\ServiceProvider;
use J3ns3n\LaravelLinkly\Client\LinklyClient;

class LinklyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    #[\Override]
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/Config/linkly.php', 'linkly'
        );

        $this->app->singleton('linkly', fn ($app): LinklyClient => new LinklyClient(
            apiKey: config('linkly.api_key'),
            baseUrl: config('linkly.api_url'),
            workspaceId: config('linkly.workspace_id'),
            retryConfig: config('linkly.retry'),
            defaultDomain: config('linkly.default_domain'),
            emailAddress: config('linkly.email'),
            timeout: config('linkly.timeout'),
        ));

        $this->app->alias('linkly', LinklyClient::class);
        $this->app->alias('linkly', 'linkly.client');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/config/linkly.php' => config_path('linkly.php'),
            ], 'linkly-config');
        }
    }
}
