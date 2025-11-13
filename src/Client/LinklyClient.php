<?php

namespace J3ns3n\LaravelLinkly\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use J3ns3n\LaravelLinkly\Exceptions\LinklyException;
use J3ns3n\LaravelLinkly\Middleware\LinklyAuthMiddleware;
use J3ns3n\LaravelLinkly\Resources\Link;
use J3ns3n\LaravelLinkly\Resources\LinkCollection;

class LinklyClient
{
    protected Client $client;
    protected string $apiKey;
    protected string $workspaceId;
    protected ?string $emailAddress;
    protected array $retryConfig;

    public function __construct(
        string $apiKey,
        string $baseUrl,
        string $workspaceId,
        string $emailAddress = null,
        int $timeout = 30,
        array $retryConfig = []
    ) {
        $this->apiKey = $apiKey;
        $this->retryConfig = $retryConfig;
        $this->workspaceId = $workspaceId;
        $this->emailAddress = $emailAddress;

        $stack = HandlerStack::create();

        $stack->push(new LinklyAuthMiddleware([
            'workspace_id' => $workspaceId,
            'api_key' => $apiKey,
            'email' => $emailAddress ?? null,
        ]));

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => $timeout,
            'handler' => $stack,
        ]);
    }

    /**
     * Create a new short link
     */
    public function createLink(array $data): Link
    {
        try {
            $response = $this->client->post('link', [
                'json' => $data,
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return new Link($body);
        } catch (GuzzleException $e) {
            throw new LinklyException('Failed to create link: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Get a link by ID
     */
    public function getLink(string $linkId): Link
    {
        try {
            $response = $this->client->get("link/{$linkId}");

            $body = json_decode($response->getBody()->getContents(), true);

            return new Link($body);
        } catch (GuzzleException $e) {
            throw new LinklyException('Failed to retrieve link: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * List all links
     */
    public function listLinks(): LinkCollection
    {
        try {
            $response = $this->client->get("workspace/{$this->workspaceId}/list_links");

            $body = json_decode($response->getBody()->getContents(), true);

            return new LinkCollection($body['links'] ?? []);
        } catch (GuzzleException $e) {
            throw new LinklyException('Failed to list links: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Update a link
     */
    public function updateLink(string $linkId, array $data): Link
    {
        try {
            $response = $this->client->post("link", [
                'json' => ['id' => $linkId, ...$data],
            ]);

            $body = json_decode($response->getBody()->getContents(), true);

            return new Link($body);
        } catch (GuzzleException $e) {
            throw new LinklyException('Failed to update link: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Delete a link
     */
    public function deleteLink(string $linkId): bool
    {
        try {
            $this->client->delete("workspace/{$this->workspaceId}/links", [
                'json' => ['ids' => [$linkId]],
            ]);

            return true;
        } catch (GuzzleException $e) {
            throw new LinklyException('Failed to delete link: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }
}