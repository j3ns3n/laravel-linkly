<?php

namespace J3ns3n\LaravelLinkly\Facades;

use Illuminate\Support\Facades\Facade;
use J3ns3n\LaravelLinkly\Resources\Link;
use J3ns3n\LaravelLinkly\Resources\LinkCollection;

/**
 * @method static Link createLink(string[] $data)
 * @method static Link getLink(string $linkId)
 * @method static LinkCollection listLinks(string[] $params = [])
 * @method static Link updateLink(string $linkId, string[] $data)
 * @method static bool deleteLink(string $linkId)
 *
 * @see \J3ns3n\LaravelLinkly\Client\LinklyClient
 */
class Linkly extends Facade
{
    /**
     * Get the registered name of the component.
     */
    #[\Override]
    protected static function getFacadeAccessor(): string
    {
        return 'linkly';
    }
}
