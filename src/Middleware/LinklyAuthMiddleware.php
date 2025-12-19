<?php

namespace J3ns3n\LaravelLinkly\Middleware;

use Closure;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\RequestInterface;

/**
 * Guzzle middleware to add body values before request is sent
 */
class LinklyAuthMiddleware
{
    /**
     * LinklyAuthMiddleware constructor.
     *
     * @param  array<string, mixed>  $defaultValues
     */
    public function __construct(
        /**
         * Default values to merge into request body
         */
        protected array $defaultValues = []
    ) {}

    /**
     * Create the middleware callable
     */
    public function __invoke(callable $handler): Closure
    {
        return function (RequestInterface $request, array $options) use ($handler): PromiseInterface {

            if ($this->isGetRequest($request)) {
                // Only modify GET requests
                $request = $this->addGetRequestValues($request);
            }
            // Only modify requests with JSON content
            elseif ($this->isJsonRequest($request)) {
                $request = $this->addJsonBodyValues($request);
            }
            // Handle form data requests
            elseif ($this->isFormRequest($request)) {
                $request = $this->addFormBodyValues($request, $options);
            }

            // Remove all null values from the request
            $request = $this->removeNullValuesFromRequest($request);

            return $handler($request, $options);
        };
    }

    /**
     * Check if request is JSON
     */
    protected function isJsonRequest(RequestInterface $request): bool
    {
        $contentType = $request->getHeaderLine('Content-Type');

        return str_contains($contentType, 'application/json');
    }

    /**
     * Check if request is form data
     */
    protected function isFormRequest(RequestInterface $request): bool
    {
        $contentType = $request->getHeaderLine('Content-Type');

        return str_contains($contentType, 'application/x-www-form-urlencoded') ||
            str_contains($contentType, 'multipart/form-data');
    }

    /**
     * Check if request is GET
     */
    protected function isGetRequest(RequestInterface $request): bool
    {
        return strtoupper($request->getMethod()) === 'GET';
    }

    /**
     * Add api_key to request url if GET request
     */
    protected function addGetRequestValues(RequestInterface $request): RequestInterface
    {
        $uri = $request->getUri();
        $queryParams = [];
        parse_str($uri->getQuery(), $queryParams);

        $queryParams['api_key'] = $this->defaultValues['api_key'] ?? '';
        // Only add workspace_id if the id is not already in the URL path
        if (! str_contains($uri->getPath(), 'workspace/')) {
            $queryParams['workspace_id'] = $this->defaultValues['workspace_id'] ?? '';
        }

        $newUri = $uri->withQuery(http_build_query($queryParams));

        return $request->withUri($newUri);
    }

    /**
     * Add values to JSON body
     */
    protected function addJsonBodyValues(RequestInterface $request): RequestInterface
    {
        $body = $request->getBody()->getContents();
        $decoded = json_decode($body, true) ?? [];

        // Merge default values (don't override existing values)
        $merged = array_merge($this->defaultValues, $decoded);

        $newBody = json_encode($merged);
        if ($newBody === false) {
            $newBody = '';
        }

        $stream = Utils::streamFor($newBody);

        return $request
            ->withBody($stream)
            ->withHeader('Content-Length', (string) strlen($newBody));
    }

    /**
     * Add values to form body (updates options)
     *
     * @param  array<string, mixed>  $options
     */
    protected function addFormBodyValues(RequestInterface $request, array &$options): RequestInterface
    {
        if (isset($options['form_params'])) {
            $options['form_params'] = array_merge($this->defaultValues, $options['form_params']);
        }

        return $request;
    }

    /**
     * Static factory method for easy use
     *
     * @param  array<string, mixed>  $values
     */
    public static function add(array $values): self
    {
        return new self($values);
    }

    /**
     * Remove all null values from the request body (for JSON/form requests)
     */
    protected function removeNullValuesFromRequest(RequestInterface $request): RequestInterface
    {
        if ($this->isJsonRequest($request)) {
            $body = $request->getBody()->getContents();
            $decoded = json_decode($body, true) ?? [];
            $filtered = array_filter($decoded, fn ($v) => $v !== null);
            $newBody = json_encode($filtered) ?: '';
            $stream = Utils::streamFor($newBody);

            return $request
                ->withBody($stream)
                ->withHeader('Content-Length', (string) strlen($newBody));
        }
        // For form requests, nulls should not be present, but filter if needed
        if ($this->isFormRequest($request)) {
            // No-op: form data is handled via $options, not request body
            return $request;
        }

        return $request;
    }
}
