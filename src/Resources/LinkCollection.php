<?php

namespace J3ns3n\LaravelLinkly\Resources;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use JsonSerializable;
use Traversable;

class LinkCollection implements IteratorAggregate, Countable, JsonSerializable
{
    protected array $links = [];

    public function __construct(array $links)
    {
        $this->links = array_map(fn($link) => new Link($link), $links);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->links);
    }

    public function count(): int
    {
        return count($this->links);
    }

    public function toArray(): array
    {
        return array_map(fn(Link $link) => $link->toArray(), $this->links);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function first(): ?Link
    {
        return $this->links[0] ?? null;
    }

    public function last(): ?Link
    {
        return $this->links[count($this->links) - 1] ?? null;
    }

    public function isEmpty(): bool
    {
        return empty($this->links);
    }
}