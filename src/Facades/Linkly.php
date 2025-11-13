<?php

namespace J3ns3n\LaravelLinkly\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \J3ns3n\LaravelLinkly\Resources\Link createLink(array $data)
 * @method static \J3ns3n\LaravelLinkly\Resources\Link getLink(string $linkId)
 * @method static \J3ns3n\LaravelLinkly\Resources\LinkCollection listLinks(array $params = [])
 * @method static \J3ns3n\LaravelLinkly\Resources\Link updateLink(string $linkId, array $data)
 * @method static bool deleteLink(string $linkId)
 * @method static array getLinkAnalytics(string $linkId, array $params = [])
 *
 * @see \J3ns3n\LaravelLinkly\Client\LinklyClient
 */
class Linkly extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'linkly';
    }
}