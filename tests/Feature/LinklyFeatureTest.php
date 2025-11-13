<?php

namespace J3ns3n\LaravelLinkly\Tests\Feature;

use Illuminate\Support\Facades\Config;
use J3ns3n\LaravelLinkly\Facades\Linkly;
use J3ns3n\LaravelLinkly\LinklyServiceProvider;
use Orchestra\Testbench\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use ReflectionClass;

class LinklyFeatureTest extends TestCase
{
    protected bool $useLiveApi;

    protected function setUp(): void
    {
        parent::setUp();
        // Set up config for the package
        Config::set('linkly.api_key', env('LINKLY_API_KEY', 'linkly-api-key'));
        Config::set('linkly.api_url', env('LINKLY_API_URL', 'https://app.linklyhq.com/api/v1/'));
        Config::set('linkly.workspace_id', env('LINKLY_WORKSPACE_ID', 'linkly-workspace-id'));
        Config::set('linkly.email', env('LINKLY_EMAIL_ADDR', null));
        Config::set('linkly.timeout', env('LINKLY_TIMEOUT', 30));
        Config::set('linkly.retry', [
            'times' => env('LINKLY_RETRY_TIMES', 1),
            'sleep' => env('LINKLY_RETRY_SLEEP', 0),
        ]);
        $this->useLiveApi = $this->shouldUseLiveApi();
    }

    protected function shouldUseLiveApi(): bool
    {
        return env('TEST_LIVE_API') && env('LINKLY_API_KEY') && env('LINKLY_API_URL') && env('LINKLY_WORKSPACE_ID');
    }

    protected function mockHttpResponses(array $responses)
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $linkly = $this->app->make('linkly');
        $reflection = new ReflectionClass($linkly);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($linkly, $client);
    }

    protected function maybeMockHttpResponses(array $responses)
    {
        if (!$this->useLiveApi) {
            $this->mockHttpResponses($responses);
        }
    }

    public function test_create_link()
    {
        $this->maybeMockHttpResponses([
            new Response(200, [], json_encode([
                'id' => 'abc123',
                'url' => 'https://lnk.ly/abc123',
                'full_url' => 'https://example.com',
            ])),
        ]);

        $link = Linkly::createLink(['url' => 'https://example.com']);
        $this->assertNotNull($link->getId());
        $this->assertNotNull($link->getShortUrl());
        $this->assertNotNull($link->getOriginalUrl());
    }

    public function test_list_links()
    {
        $this->maybeMockHttpResponses([
            new Response(200, [], json_encode([
                'links' => [
                    ['id' => 'abc123', 'url' => 'https://lnk.ly/abc123', 'full_url' => 'https://example.com'],
                    ['id' => 'def456', 'url' => 'https://lnk.ly/def456', 'full_url' => 'https://another.com'],
                ],
            ])),
        ]);

        $collection = Linkly::listLinks();
        $this->assertGreaterThanOrEqual(0, $collection->count());
        if ($collection->count() > 0) {
            $this->assertNotNull($collection->first()->getId());
        }
    }

    protected function getPackageProviders($app)
    {
        return [LinklyServiceProvider::class];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Linkly' => Linkly::class,
        ];
    }
}
