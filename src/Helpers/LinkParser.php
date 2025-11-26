<?php

namespace J3ns3n\LaravelLinkly\Helpers;

use J3ns3n\LaravelLinkly\Resources\Link;
use Psr\Http\Message\ResponseInterface;

class LinkParser
{
    /**
     * @param  ResponseInterface|array<string, mixed>  $response
     */
    public static function createLinkFromResponse(ResponseInterface|array $response): Link
    {
        if ($response instanceof ResponseInterface) {
            $body = json_decode($response->getBody()->getContents(), true);
        } else {
            $body = $response;
        }

        return new Link(
            id: $body['id'],
            url: $body['url'],
            full_url: $body['full_url'],
            fb_pixel_id: $body['fb_pixel_id'] ?? null,
            hide_referrer: $body['hide_referrer'] ?? null,
            expiry_datetime: $body['expiry_datetime'] ?? null,
            expiry_destination: $body['expiry_destination'] ?? null,
            rules: $body['rules'] ?? null,
            cloaking: $body['cloaking'] ?? null,
            linkify_words: $body['linkify_words'] ?? null,
            og_description: $body['og_description'] ?? null,
            body_tags: $body['body_tags'] ?? null,
            og_title: $body['og_title'] ?? null,
            note: $body['note'] ?? null,
            name: $body['name'] ?? null,
            gtm_id: $body['gtm_id'] ?? null,
            og_image: $body['og_image'] ?? null,
            block_bots: $body['block_bots'] ?? null,
            utm_content: $body['utm_content'] ?? null,
            enabled: $body['enabled'] ?? null,
            replacements: $body['replacements'] ?? null,
            deleted: $body['deleted'] ?? null,
            workspace_id: $body['workspace_id'] ?? null,
            public_analytics: $body['public_analytics'] ?? null,
            utm_source: $body['utm_source'] ?? null,
            slug: $body['slug'] ?? null,
            domain: $body['domain'] ?? null,
            forward_params: $body['forward_params'] ?? null,
            utm_medium: $body['utm_medium'] ?? null,
            head_tags: $body['head_tags'] ?? null,
            ga4_tag_id: $body['ga4_tag_id'] ?? null,
            utm_term: $body['utm_term'] ?? null,
            utm_campaign: $body['utm_campaign'] ?? null,
        );
    }
}
