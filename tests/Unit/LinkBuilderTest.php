<?php

declare(strict_types=1);

namespace J3ns3n\LaravelLinkly\Unit;

use J3ns3n\LaravelLinkly\Builders\LinkBuilder;
use J3ns3n\LaravelLinkly\Client\LinklyClient;
use J3ns3n\LaravelLinkly\Exceptions\LinklyException;
use J3ns3n\LaravelLinkly\Resources\Link;
use Mockery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(LinkBuilder::class)]
class LinkBuilderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_builder_creates_link_with_minimal_data()
    {
        $mockClient = Mockery::mock(LinklyClient::class);
        $mockLink = Mockery::mock(Link::class);
        $mockClient->shouldReceive('createLink')
            ->once()
            ->with(['url' => 'https://example.com'])
            ->andReturn($mockLink);

        $builder = new LinkBuilder($mockClient, 'https://example.com');
        $link = $builder->create();
        $this->assertSame($mockLink, $link);
    }

    public function test_builder_chains_methods_and_creates_link()
    {
        $mockClient = Mockery::mock(LinklyClient::class);
        $mockLink = Mockery::mock(Link::class);
        $expected = [
            'url' => 'https://example.com',
            'name' => 'Test Link',
            'slug' => 'test-slug',
            'domain' => 'custom.domain',
            'cloaking' => true,
            'block_bots' => true,
            'forward_params' => false,
            'webhooks' => ['https://webhook.site/abc'],
            'utm_source' => 'newsletter',
            'utm_medium' => 'email',
            'utm_campaign' => 'launch',
            'og_title' => 'OG Title',
            'og_description' => 'OG Desc',
            'og_image' => 'https://img.com/og.png',
            'expiry_datetime' => '2025-12-31T23:59:59Z',
            'expiry_destination' => 'https://expired.com',
            'rules' => ['matches' => 'US', 'percentage' => 50, 'url' => 'https://us.com', 'what' => 'geo'],
        ];
        $mockClient->shouldReceive('createLink')
            ->once()
            ->with(Mockery::on(function ($data) use ($expected) {
                foreach ($expected as $key => $value) {
                    if (! isset($data[$key]) || $data[$key] !== $value) {
                        return false;
                    }
                }

                return true;
            }))
            ->andReturn($mockLink);

        $builder = new LinkBuilder($mockClient, 'https://example.com');
        $link = $builder
            ->name('Test Link')
            ->slug('test-slug')
            ->domain('custom.domain')
            ->cloaking()
            ->blockBots()
            ->forwardParams(false)
            ->webhook('https://webhook.site/abc')
            ->utm('newsletter', 'email', 'launch')
            ->openGraph('OG Title', 'OG Desc', 'https://img.com/og.png')
            ->expires('2025-12-31T23:59:59Z', 'https://expired.com')
            ->rules(['matches' => 'US', 'percentage' => 50, 'url' => 'https://us.com', 'what' => 'geo'])
            ->create();
        $this->assertSame($mockLink, $link);
    }

    public function test_builder_throws_exception_on_client_error()
    {
        $mockClient = Mockery::mock(LinklyClient::class);
        $mockClient->shouldReceive('createLink')
            ->once()
            ->andThrow(new LinklyException('API error'));
        $builder = new LinkBuilder($mockClient, 'https://fail.com');
        $this->expectException(LinklyException::class);
        $builder->create();
    }
}
