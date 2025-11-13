<?php

namespace J3ns3n\LaravelLinkly\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use J3ns3n\LaravelLinkly\Client\LinklyClient;
use J3ns3n\LaravelLinkly\Exceptions\LinklyException;
use PHPUnit\Framework\TestCase;

class LinklyClientTest extends TestCase
{
    protected function createMockClient(array $responses): LinklyClient
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $linklyClient = new LinklyClient('test-api-key', 'https://api.linkly.com/v1', 'test-workspace-id');

        // Use reflection to inject the mock client
        $reflection = new \ReflectionClass($linklyClient);
        $property = $reflection->getProperty('client');
        $property->setAccessible(true);
        $property->setValue($linklyClient, $client);

        return $linklyClient;
    }

    public function test_create_link_success()
    {
        $mockResponse = new Response(200, [], json_encode([
            'id' => 'abc123',
            'url' => 'https://lnk.ly/abc123',
            'full_url' => 'https://example.com',
        ]));

        $client = $this->createMockClient([$mockResponse]);

        $link = $client->createLink([
            'url' => 'https://example.com',
        ]);

        $this->assertEquals('abc123', $link->getId());
        $this->assertEquals('https://lnk.ly/abc123', $link->getShortUrl());
        $this->assertEquals('https://example.com', $link->getOriginalUrl());
    }

    public function test_get_link_success()
    {
        $mockResponse = new Response(200, [], json_encode([
            'id' => 'abc123',
            'url' => 'https://lnk.ly/abc123',
            'full_url' => 'https://example.com',
        ]));

        $client = $this->createMockClient([$mockResponse]);

        $link = $client->getLink('abc123');

        $this->assertEquals('abc123', $link->getId());
    }

    public function test_create_link_throws_exception_on_error()
    {
        $this->expectException(LinklyException::class);

        $mockResponse = new Response(400, [], json_encode([
            'error' => 'Invalid URL',
        ]));

        $client = $this->createMockClient([$mockResponse]);

        $client->createLink([
            'full_url' => 'invalid-url',
        ]);
    }

    public function test_list_links_returns_collection()
    {
        $mockResponse = new Response(200, [], json_encode([
            'links' => [
                [
                    'id' => 'link1',
                    'url' => 'https://lnk.ly/link1',
                    'full_url' => 'https://example.com/1',
                ],
                [
                    'id' => 'link2',
                    'url' => 'https://lnk.ly/link2',
                    'full_url' => 'https://example.com/2',
                ],
            ],
        ]));

        $client = $this->createMockClient([$mockResponse]);

        $links = $client->listLinks();

        $this->assertCount(2, $links);
        $this->assertEquals('link1', $links->first()->getId());
        $this->assertEquals('link2', $links->last()->getId());
    }
}