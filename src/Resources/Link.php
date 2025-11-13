<?php

namespace J3ns3n\LaravelLinkly\Resources;

use ArrayAccess;
use JsonSerializable;

class Link implements ArrayAccess, JsonSerializable
{
    protected array $attributes;

    public function __construct(array $attributes)
    {
        $this->attributes = $attributes;
    }

    public function __get(string $name)
    {
        return $this->attributes[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->attributes[$name] = $value;
    }

    public function __isset(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        return $this->attributes[$offset] ?? null;
    }

    public function offsetSet($offset, $value): void
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset]);
    }

    public function toArray(): array
    {
        return $this->attributes;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getId(): ?string
    {
        return $this->attributes['id'] ?? null;
    }

    public function getShortUrl(): ?string
    {
        return $this->attributes['url'] ?? null;
    }

    public function getOriginalUrl(): ?string
    {
        return $this->attributes['full_url'] ?? null;
    }
}