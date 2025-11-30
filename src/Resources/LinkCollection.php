<?php

namespace J3ns3n\LaravelLinkly\Resources;

use ArrayIterator;
use Illuminate\Support\Collection;
use J3ns3n\LaravelLinkly\Helpers\LinkParser;
use JsonSerializable;
use Traversable;

/**
 * @extends Collection<int, Link>
 */
class LinkCollection extends Collection implements JsonSerializable
{
    /**
     * @var array<int, Link>
     */
    protected $items = [];

    /**
     * @param  array<int, array<string, mixed>>  $links
     */
    public function __construct(array $links)
    {
        $this->items = array_map(LinkParser::createLinkFromResponse(...), $links);
    }

    #[\Override]
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }

    #[\Override]
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @return array<int, Link>
     */
     #[\Override]
    public function toArray(): array
    {
        return $this->items;
    }

    /**
     * Returns an array of arrays for serialization
     *
     * @return array<int, array<string, mixed>>
     */
    public function toArrayOfArrays(): array
    {
        return array_map(fn (Link $link): array => $link->toArray(), $this->items);
    }

    #[\Override]
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    #[\Override]
    public function isEmpty(): bool
    {
        return $this->items === [];
    }
}
