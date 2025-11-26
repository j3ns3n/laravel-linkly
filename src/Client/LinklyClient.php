<?php

namespace J3ns3n\LaravelLinkly\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use J3ns3n\LaravelLinkly\Exceptions\LinklyException;
use J3ns3n\LaravelLinkly\Helpers\LinkParser;
use J3ns3n\LaravelLinkly\Middleware\LinklyAuthMiddleware;
use J3ns3n\LaravelLinkly\Resources\Link;
use J3ns3n\LaravelLinkly\Resources\LinkCollection;

class LinklyClient
{
    protected Client $client;

    /**
     * LinklyClient constructor.
     *
     * @param  array{'times': int, 'sleep': int}  $retryConfig
     */
    public function __construct(
        protected string $apiKey,
        string $baseUrl,
        protected string $workspaceId,
        protected array $retryConfig,
        protected ?string $emailAddress,
        int $timeout = 30,
    ) {
        $stack = HandlerStack::create();

        $stack->push(new LinklyAuthMiddleware([
            'workspace_id' => $this->workspaceId,
            'api_key' => $this->apiKey,
            'email' => $this->emailAddress ?? null,
        ]));

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => $timeout,
            'handler' => $stack,
        ]);
    }

    /**
     * Create a new link
     *
     * @param array{
     *     url: string,
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
    public function createLink(array $data): Link
    {
        try {
            $response = $this->client->post('link', [
                'json' => $data,
            ]);

            return LinkParser::createLinkFromResponse($response);
        } catch (GuzzleException $guzzleException) {
            throw new LinklyException('Failed to create link: '.$guzzleException->getMessage(), $guzzleException->getCode(), $guzzleException);
        }
    }

    /**
     * Get a link by ID
     *
     * @throws LinklyException
     */
    public function getLink(string $linkId): Link
    {
        try {
            $response = $this->client->get('link/'.$linkId);

            return LinkParser::createLinkFromResponse($response);
        } catch (GuzzleException $guzzleException) {
            throw new LinklyException('Failed to retrieve link: '.$guzzleException->getMessage(), $guzzleException->getCode(), $guzzleException);
        }
    }

    /**
     * List all links
     *
     * @throws LinklyException
     */
    public function listLinks(): LinkCollection
    {
        try {
            $response = $this->client->get(sprintf('workspace/%s/list_links', $this->workspaceId));

            $body = json_decode($response->getBody()->getContents(), true);

            return new LinkCollection($body['links'] ?? []);
        } catch (GuzzleException $guzzleException) {
            throw new LinklyException('Failed to list links: '.$guzzleException->getMessage(), $guzzleException->getCode(), $guzzleException);
        }
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
    public function updateLink(string $linkId, array $data): Link
    {
        try {
            $response = $this->client->post('link', [
                'json' => ['id' => $linkId, ...$data],
            ]);

            return LinkParser::createLinkFromResponse($response);
        } catch (GuzzleException $guzzleException) {
            throw new LinklyException('Failed to update link: '.$guzzleException->getMessage(), $guzzleException->getCode(), $guzzleException);
        }
    }

    /**
     * Delete a link
     *
     * @throws LinklyException
     */
    public function deleteLink(string $linkId): bool
    {
        try {
            $this->client->delete(sprintf('workspace/%s/links', $this->workspaceId), [
                'json' => ['ids' => [$linkId]],
            ]);

            return true;
        } catch (GuzzleException $guzzleException) {
            throw new LinklyException('Failed to delete link: '.$guzzleException->getMessage(), $guzzleException->getCode(), $guzzleException);
        }
    }
}
