# Linkly Laravel Package

A Laravel package for interacting with the Linkly API.

## Installation

```bash
composer require j3ns3n/laravel-linkly
```

## Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --tag=linkly-config
```

Add your Linkly API key to your `.env` file:

```env
LINKLY_API_KEY=your-api-key-here
LINKLY_API_URL=https://app.linklyhq.com/api/v1/
LINKLY_WORKSPACE_ID=your-workspace-id
LINKLY_TIMEOUT=30
LINKLY_WORKSPACE_ID=your-workspace-id
```

## Usage

### Using the Facade

```php
use J3ns3n\LaravelLinkly\Facades\Linkly;

// Create a new short link
$link = Linkly::createLink([
    'url' => 'https://example.com/very-long-url',
    'name' => 'My Example Link',
]);

echo $link->getShortUrl();
echo $link->getId();
```

### Using Dependency Injection

```php
use J3ns3n\LaravelLinkly\Client\LinklyClient;

class LinkController extends Controller
{
    public function __construct(protected LinklyClient $linkly)
    {
    }

    public function store(Request $request)
    {
        $link = $this->linkly->createLink([
            'url' => $request->input('url'),
            'title' => $request->input('title'),
        ]);

        return response()->json($link);
    }
}
```

### Available Methods

```php
// Create a link
$link = Linkly::createLink([
    'url' => 'https://example.com',
    'title' => 'Example',
]);

// Get a link by ID
$link = Linkly::getLink('link-id');

// List all links with pagination
$links = Linkly::listLinks([
    'page' => 1,
    'per_page' => 20,
]);

foreach ($links as $link) {
    echo $link->getShortUrl();
}

// Update a link
$link = Linkly::updateLink('link-id', [
    'title' => 'Updated Title',
]);

// Delete a link
Linkly::deleteLink('link-id');
```

### Working with Link Resources

```php
$link = Linkly::getLink('link-id');

// Access properties
echo $link->getId();
echo $link->getShortUrl();
echo $link->getOriginalUrl();

// Convert to array
$array = $link->toArray();

// Convert to JSON
$json = json_encode($link);
```

### Error Handling

```php
use J3ns3n\LaravelLinkly\Exceptions\LinklyException;

try {
    $link = Linkly::createLink([
        'url' => 'https://example.com',
    ]);
} catch (LinklyException $e) {
    Log::error('Linkly API error: ' . $e->getMessage());
    return response()->json(['error' => 'Failed to create link'], 500);
}
```

## Testing

```bash
composer test
```

## License

MIT