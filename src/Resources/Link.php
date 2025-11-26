<?php

namespace J3ns3n\LaravelLinkly\Resources;

use JsonSerializable;

class Link implements JsonSerializable
{
    /**
     * @param  array{matches: ?string, percentage: ?int, url: ?string,what: ?string}|null  $rules
     */
    public function __construct(
        public int $id,
        public string $url,
        public string $full_url,
        public ?string $fb_pixel_id,
        public ?bool $hide_referrer,
        public ?string $expiry_datetime,
        public ?string $expiry_destination,
        public ?array $rules,
        public ?bool $cloaking,
        public ?string $linkify_words,
        public ?string $og_description,
        public ?string $body_tags,
        public ?string $og_title,
        public ?string $note,
        public ?string $name,
        public ?string $gtm_id,
        public ?string $og_image,
        public ?bool $block_bots,
        public ?string $utm_content,
        public ?bool $enabled,
        public ?string $replacements,
        public ?bool $deleted,
        public ?int $workspace_id,
        public ?bool $public_analytics,
        public ?string $utm_source,
        public ?string $slug,
        public ?string $domain,
        public ?bool $forward_params,
        public ?string $utm_medium,
        public ?string $head_tags,
        public ?string $ga4_tag_id,
        public ?string $utm_term,
        public ?string $utm_campaign,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'full_url' => $this->full_url,
            'fb_pixel_id' => $this->fb_pixel_id,
            'hide_referrer' => $this->hide_referrer,
            'expiry_datetime' => $this->expiry_datetime,
            'expiry_destination' => $this->expiry_destination,
            'rules' => $this->rules,
            'cloaking' => $this->cloaking,
            'linkify_words' => $this->linkify_words,
            'og_description' => $this->og_description,
            'body_tags' => $this->body_tags,
            'og_title' => $this->og_title,
            'note' => $this->note,
            'name' => $this->name,
            'gtm_id' => $this->gtm_id,
            'og_image' => $this->og_image,
            'block_bots' => $this->block_bots,
            'utm_content' => $this->utm_content,
            'enabled' => $this->enabled,
            'replacements' => $this->replacements,
            'deleted' => $this->deleted,
            'workspace_id' => $this->workspace_id,
            'public_analytics' => $this->public_analytics,
            'utm_source' => $this->utm_source,
            'slug' => $this->slug,
            'domain' => $this->domain,
            'forward_params' => $this->forward_params,
            'utm_medium' => $this->utm_medium,
            'head_tags' => $this->head_tags,
            'ga4_tag_id' => $this->ga4_tag_id,
            'utm_term' => $this->utm_term,
            'utm_campaign' => $this->utm_campaign,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getShortUrl(): ?string
    {
        return $this->full_url;
    }

    public function getOriginalUrl(): string
    {
        return $this->url;
    }
}
