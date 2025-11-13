<?php

namespace J3ns3n\LaravelLinkly;

use Illuminate\Support\ServiceProvider;
use J3ns3n\LaravelLinkly\Client\LinklyClient;

class LinklyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/linkly.php', 'linkly'
        );

        $this->app->singleton('linkly', function ($app) {
            return new LinklyClient(
                config('linkly.api_key'),
                config('linkly.api_url'),
                config('linkly.workspace_id'),
                config('linkly.email'),
                config('linkly.timeout'),
                config('linkly.retry')
            );
        });

        $this->app->alias('linkly', LinklyClient::class);
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