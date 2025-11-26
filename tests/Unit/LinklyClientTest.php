<?php

declare(strict_types=1);

namespace J3ns3n\LaravelLinkly\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use J3ns3n\LaravelLinkly\Client\LinklyClient;
use J3ns3n\LaravelLinkly\Exceptions\LinklyException;
use PHPUnit\Framework\TestCase;

final class LinklyClientTest extends TestCase
{
    /**
     * @param  \GuzzleHttp\Psr7\Response[]  $responses
     */
    protected function createMockClient(array $responses): LinklyClient
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $linklyClient = new LinklyClient(
            apiKey: 'test-api-key',
            baseUrl: 'https://api.linkly.com/v1',
            workspaceId: 'test-workspace-id',
            retryConfig: [
                'times' => 1,
                'sleep' => 0,
            ],
            emailAddress: '');

        // Use reflection to inject the mock client
        $reflection = new \ReflectionClass($linklyClient);
        $property = $reflection->getProperty('client');
        $property->setValue($linklyClient, $client);

        return $linklyClient;
    }

    public function test_create_link_success(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'id' => 123,
            'full_url' => 'https://lnk.ly/abc123',
            'url' => 'https://example.com',
        ]));

        $client = $this->createMockClient([$mockResponse]);

        $link = $client->createLink([
            'url' => 'https://example.com',
        ]);

        $this->assertSame(123, $link->getId());
        $this->assertSame('https://lnk.ly/abc123', $link->getShortUrl());
        $this->assertSame('https://example.com', $link->getOriginalUrl());
    }

    public function test_get_link_success(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'id' => 123,
            'full_url' => 'https://lnk.ly/abc123',
            'url' => 'https://example.com',
        ]));

        $client = $this->createMockClient([$mockResponse]);

        $link = $client->getLink('123');

        $this->assertSame(123, $link->getId());
    }

    public function test_create_link_throws_exception_on_error(): void
    {
        $this->expectException(LinklyException::class);

        $mockResponse = new Response(400, [], json_encode([
            'error' => 'Invalid URL',
        ]));

        $client = $this->createMockClient([$mockResponse]);

        $client->createLink([
            'url' => 'invalid-url',
        ]);
    }

    public function test_list_links_returns_collection(): void
    {
        $mockResponse = new Response(200, [], json_encode([
            'links' => [
                [
                    'id' => 123,
                    'full_url' => 'https://lnk.ly/link1',
                    'url' => 'https://example.com/1',
                ],
                [
                    'id' => 456,
                    'full_url' => 'https://lnk.ly/link2',
                    'url' => 'https://example.com/2',
                ],
            ],
        ]));

        $client = $this->createMockClient([$mockResponse]);

        $links = $client->listLinks();

        $this->assertCount(2, $links);
        $this->assertEquals(123, $links->first()->getId());
        $this->assertEquals(456, $links->last()->getId());
    }
}
