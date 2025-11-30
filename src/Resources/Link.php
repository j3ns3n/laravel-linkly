<?php

namespace J3ns3n\LaravelLinkly\Resources;

use J3ns3n\LaravelLinkly\Exceptions\LinklyException;
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
        public ?string $fb_pixel_id = null,
        public ?bool $hide_referrer = null,
        public ?string $expiry_datetime = null,
        public ?string $expiry_destination = null,
        public ?array $rules = ['matches' => null, 'percentage' => null, 'url' => null, 'what' => null],
        public ?bool $cloaking = null,
        public ?string $linkify_words = null,
        public ?string $og_description = null,
        public ?string $body_tags = null,
        public ?string $og_title = null,
        public ?string $note = null,
        public ?string $name = null,
        public ?string $gtm_id = null,
        public ?string $og_image = null,
        public ?bool $block_bots = null,
        public ?string $utm_content = null,
        public ?bool $enabled = null,
        public ?string $replacements = null,
        public ?bool $deleted = null,
        public ?int $workspace_id = null,
        public ?bool $public_analytics = null,
        public ?string $utm_source = null,
        public ?string $slug = null,
        public ?string $domain = null,
        public ?bool $forward_params = null,
        public ?string $utm_medium = null,
        public ?string $head_tags = null,
        public ?string $ga4_tag_id = null,
        public ?string $utm_term = null,
        public ?string $utm_campaign = null,
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
     #[\Override]
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

    /**
     * Update a link
     *
     * @param array{
     *     url?: string,
     *     fb_pixel_id?: string,
     *     hide_referrer?: bool,
     *     expiry_datetime?: string,
     *     expiry_destination?: string,
     *     rules?: array{
     *      matches: ?string,
     *      percentage: ?int,
     *      url: ?string,
     *      what: ?string,
     *     },
     *     cloaking?: bool,
     *     linkify_words?: string,
     *     og_description?: string,
     *     body_tags?: string,
     *     og_title?: string,
     *     note?: string,
     *     name?: string,
     *     gtm_id?: string,
     *     og_image?: string,
     *     block_bots?: bool,
     *     utm_content?: string,
     *     enabled?: bool,
     *     replacements?: string,
     *     public_analytics?: bool,
     *     utm_source?: string,
     *     slug?: string,
     *     domain?: string,
     *     forward_params?: bool,
     *     utm_medium?: string,
     *     head_tags?: string,
     *     ga4_tag_id?: string,
     *     utm_term?: string,
     *     utm_campaign?: string
     * } $data
     *
     * @throws LinklyException
     */
    public function update(array $data): Link
    {
        return app('linkly.client')->updateLink((string) $this->id, $data);
    }

    /**
     * @throws LinklyException
     */
    public function delete(): bool
    {
        return app('linkly.client')->deleteLink((string) $this->id);
    }
}
