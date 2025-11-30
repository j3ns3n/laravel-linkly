<?php

declare(strict_types=1);

namespace J3ns3n\LaravelLinkly\Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Config;
use J3ns3n\LaravelLinkly\Client\LinklyClient;
use J3ns3n\LaravelLinkly\Facades\Linkly;
use J3ns3n\LaravelLinkly\Helpers\LinkParser;
use J3ns3n\LaravelLinkly\LinklyServiceProvider;
use J3ns3n\LaravelLinkly\Middleware\LinklyAuthMiddleware;
use J3ns3n\LaravelLinkly\Resources\Link;
use J3ns3n\LaravelLinkly\Resources\LinkCollection;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use ReflectionClass;

#[CoversClass(LinklyClient::class)]
#[CoversClass(Linkly::class)]
#[CoversClass(LinkParser::class)]
#[CoversClass(LinklyServiceProvider::class)]
#[CoversClass(LinklyAuthMiddleware::class)]
#[CoversClass(Link::class)]
#[CoversClass(LinkCollection::class)]
final class LinklyFeatureTest extends TestCase
{
    protected bool $useLiveApi;

    protected function setUp(): void
    {
        parent::setUp();
        // Set up config for the package
        Config::set('linkly.api_key', env('LINKLY_API_KEY', 'linkly-api-key'));
        Config::set('linkly.api_url', env('LINKLY_API_URL', 'https://app.linklyhq.com/api/v1/'));
        Config::set('linkly.workspace_id', env('LINKLY_WORKSPACE_ID', 'linkly-workspace-id'));
        Config::set('linkly.email', env('LINKLY_EMAIL_ADDR'));
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

    /**
     * @param  Response[]  $responses
     */
    protected function mockHttpResponses(array $responses)
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);
        $linkly = $this->app->make('linkly');
        $reflection = new ReflectionClass($linkly);
        $property = $reflection->getProperty('client');
        $property->setValue($linkly, $client);
    }

    /**
     * @param  Response[]  $responses
     */
    protected function maybeMockHttpResponses(array $responses)
    {
        if (! $this->useLiveApi) {
            $this->mockHttpResponses($responses);
        }
    }

    public function test_create_link(): void
    {
        $this->maybeMockHttpResponses([
            new Response(200, [], json_encode([
                'id' => 123,
                'url' => 'https://lnk.ly/abc123',
                'full_url' => 'https://example.com',
            ])),
        ]);

        $link = Linkly::createLink(['url' => 'https://example.com']);
        $this->assertNotNull($link->getId());
        $this->assertNotNull($link->getShortUrl());
        $this->assertNotNull($link->getOriginalUrl());
    }

    public function test_list_links(): void
    {
        $this->maybeMockHttpResponses([
            new Response(200, [], json_encode([
                'links' => [
                    ['id' => 123, 'url' => 'https://lnk.ly/abc123', 'full_url' => 'https://example.com'],
                    ['id' => 456, 'url' => 'https://lnk.ly/def456', 'full_url' => 'https://another.com'],
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
