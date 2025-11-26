<?php

declare(strict_types=1);

namespace J3ns3n\LaravelLinkly\Unit;

use J3ns3n\LaravelLinkly\Exceptions\LinklyException;
use J3ns3n\LaravelLinkly\Resources\Link;
use Mockery;
use PHPUnit\Framework\TestCase;

class LinkTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testUpdateReturnsUpdatedLink()
    {
        $mockClient = Mockery::mock();
        $updatedLink = new Link(
            1,
            'https://original.com',
            'https://short.ly/abc',
            null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null, null
        );
        $mockClient->shouldReceive('updateLink')
            ->once()
            ->with('1', ['url' => 'https://updated.com'])
            ->andReturn($updatedLink);

        app()->instance('linkly.client', $mockClient);

        $link = new Link(
            1,
            'https://original.com',
            'https://short.ly/abc',
        );

        $result = $link->update(['url' => 'https://updated.com']);
        $this->assertInstanceOf(Link::class, $result);
        $this->assertEquals('https://original.com', $result->url);
    }

    public function testUpdateThrowsException()
    {
        $mockClient = Mockery::mock();
        $mockClient->shouldReceive('updateLink')
            ->once()
            ->andThrow(new LinklyException('Update failed'));
        app()->instance('linkly.client', $mockClient);

        $link = new Link(
            1,
            'https://original.com',
            'https://short.ly/abc'
        );

        $this->expectException(LinklyException::class);
        $link->update(['url' => 'https://fail.com']);
    }

    public function testDeleteReturnsTrue()
    {
        $mockClient = Mockery::mock();
        $mockClient->shouldReceive('deleteLink')
            ->once()
            ->with('1')
            ->andReturn(true);
        app()->instance('linkly.client', $mockClient);

        $link = new Link(
            1,
            'https://original.com',
            'https://short.ly/abc'
        );

        $this->assertTrue($link->delete());
    }

    public function testDeleteReturnsFalse()
    {
        $mockClient = Mockery::mock();
        $mockClient->shouldReceive('deleteLink')
            ->once()
            ->with('1')
            ->andReturn(false);
        app()->instance('linkly.client', $mockClient);

        $link = new Link(
            1,
            'https://original.com',
            'https://short.ly/abc'
        );

        $this->assertFalse($link->delete());
    }

    public function testDeleteThrowsException()
    {
        $mockClient = Mockery::mock();
        $mockClient->shouldReceive('deleteLink')
            ->once()
            ->andThrow(new LinklyException('Delete failed'));
        app()->instance('linkly.client', $mockClient);

        $link = new Link(
            1,
            'https://original.com',
            'https://short.ly/abc'
        );

        $this->expectException(LinklyException::class);
        $link->delete();
    }
}
