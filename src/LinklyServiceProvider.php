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
            config('linkly.api_key'),
            config('linkly.api_url'),
            config('linkly.workspace_id'),
            config('linkly.retry'),
            config('linkly.email'),
            config('linkly.timeout'),
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
