<?php

namespace J3ns3n\LaravelLinkly\Builders;

use J3ns3n\LaravelLinkly\Client\LinklyClient;
use J3ns3n\LaravelLinkly\Exceptions\LinklyException;
use J3ns3n\LaravelLinkly\Resources\Link;

class LinkBuilder
{
    /**
     * @var array<string, mixed>
     */
    protected array $data = [
        'url' => '',
    ];

    public function __construct(
        protected LinklyClient $client,
        string $url
    ) {
        $this->data['url'] = $url;
    }

    /**
     * Add a single webhook URL
     */
    public function webhook(string $url): self
    {
        $this->data['webhooks'][] = $url;

        return $this;
    }

    /**
     * Add multiple webhook URLs
     *
     * @param  string[]  $urls
     */
    public function webhooks(array $urls): self
    {
        $this->data['webhooks'] = array_merge($this->data['webhooks'] ?? [], $urls);

        return $this;
    }

    /**
     * Set the link name
     */
    public function name(string $name): self
    {
        $this->data['name'] = $name;

        return $this;
    }

    /**
     * Set the custom slug
     */
    public function slug(string $slug): self
    {
        $this->data['slug'] = $slug;

        return $this;
    }

    /**
     * Set the custom domain
     */
    public function domain(string $domain): self
    {
        $this->data['domain'] = $domain;

        return $this;
    }

    /**
     * Enable/disable link cloaking
     */
    public function cloaking(bool $enabled = true): self
    {
        $this->data['cloaking'] = $enabled;

        return $this;
    }

    /**
     * Enable/disable bot blocking
     */
    public function blockBots(bool $enabled = true): self
    {
        $this->data['block_bots'] = $enabled;

        return $this;
    }

    /**
     * Enable/disable parameter forwarding
     */
    public function forwardParams(bool $enabled = true): self
    {
        $this->data['forward_params'] = $enabled;

        return $this;
    }

    /**
     * Set UTM parameters
     */
    public function utm(
        ?string $source = null,
        ?string $medium = null,
        ?string $campaign = null,
        ?string $term = null,
        ?string $content = null
    ): self {
        if ($source) {
            $this->data['utm_source'] = $source;
        }
        if ($medium) {
            $this->data['utm_medium'] = $medium;
        }
        if ($campaign) {
            $this->data['utm_campaign'] = $campaign;
        }
        if ($term) {
            $this->data['utm_term'] = $term;
        }
        if ($content) {
            $this->data['utm_content'] = $content;
        }

        return $this;
    }

    /**
     * Set Open Graph metadata
     */
    public function openGraph(
        ?string $title = null,
        ?string $description = null,
        ?string $image = null
    ): self {
        if ($title) {
            $this->data['og_title'] = $title;
        }
        if ($description) {
            $this->data['og_description'] = $description;
        }
        if ($image) {
            $this->data['og_image'] = $image;
        }

        return $this;
    }

    /**
     * Set expiry
     */
    public function expires(string $datetime, ?string $destination = null): self
    {
        $this->data['expiry_datetime'] = $datetime;
        if ($destination) {
            $this->data['expiry_destination'] = $destination;
        }

        return $this;
    }

    /**
     * Add redirect rules
     *
     * @param array{
     *      matches: ?string,
     *      percentage: ?int,
     *      url: ?string,
     *      what: ?string,
     * } $rules
     */
    public function rules(array $rules): self
    {
        $this->data['rules'] = $rules;

        return $this;
    }

    /**
     * Set any additional data
     *
     * @param  array<string, mixed>  $data
     */
    public function with(array $data): self
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    /**
     * Create the link and add webhooks if specified
     *
     * @throws LinklyException
     */
    public function create(): Link
    {
        return $this->client->createLink($this->data);
    }
}
