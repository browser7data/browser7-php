# Browser7 PHP SDK

---

Official PHP client for the [Browser7](https://browser7.com) web scraping and rendering API.

Browser7 provides geo-targeted web scraping with automatic proxy management, CAPTCHA solving, and powerful wait actions for dynamic content.

## Features

- 🌍 **Geo-Targeting** - Render pages from specific countries and cities using residential proxies
- 🤖 **CAPTCHA Solving** - Automatic detection and solving of reCAPTCHA and Cloudflare Turnstile
- 📸 **Screenshots** - Capture viewport or full-page screenshots in PNG or JPEG
- 🌐 **In-Browser Fetch** - Fetch additional URLs through the rendered page's browser context
- ⏱️ **Wait Actions** - Click elements, wait for selectors, text content, or delays
- 🚀 **Performance** - Block images, track bandwidth, view timing breakdowns
- 🔄 **Automatic Polling** - Built-in polling with progress callbacks
- 🐘 **PHP 8.0+** - Full type declarations for IDE support

## Installation

```bash
composer require browser7/sdk
```

**Requirements:** PHP 8.0+

## Quick Start

```php
<?php

require 'vendor/autoload.php';

use Browser7\Browser7Client;

$client = new Browser7Client('your-api-key');

// Simple render
$result = $client->render('https://example.com');
echo $result->html;
```

## Authentication

Get your API key from the [Browser7 Dashboard](https://dashboard.browser7.com).

```php
$client = new Browser7Client('b7_your_api_key_here');
```

## Usage Examples

### Basic Rendering

```php
$result = $client->render('https://example.com', [
    'countryCode' => 'US'
]);

echo $result->html;              // Rendered HTML
print_r($result->selectedCity);  // City used for rendering
```

### With Wait Actions

```php
use Browser7\WaitAction;

$result = $client->render('https://example.com', [
    'countryCode' => 'GB',
    'city' => 'london',
    'waitFor' => [
        WaitAction::click('.cookie-accept'),          // Click element
        WaitAction::selector('.main-content'),         // Wait for element
        WaitAction::delay(2000)                        // Wait 2 seconds
    ]
]);
```

### With CAPTCHA Solving

```php
$result = $client->render('https://protected-site.com', [
    'countryCode' => 'US',
    'captcha' => 'auto'  // Auto-detect and solve CAPTCHAs
]);

print_r($result->captcha);  // CAPTCHA detection info
```

### With Screenshots

```php
$result = $client->render('https://example.com', [
    'countryCode' => 'US',
    'includeScreenshot' => true,      // Enable screenshot
    'screenshotFormat' => 'jpeg',     // 'jpeg' or 'png'
    'screenshotQuality' => 80,        // 1-100 (JPEG only)
    'screenshotFullPage' => false     // false = viewport only, true = full page
]);

// Save screenshot to file
file_put_contents('screenshot.jpg', base64_decode($result->screenshot));
```

### Fetch Additional URLs

```php
$result = $client->render('https://example.com', [
    'fetchUrls' => [
        'https://example.com/api/data',
        'https://example.com/api/user'
    ]
]);

print_r($result->fetchResponses);  // Array of fetch responses
```

### Check Account Balance

```php
$balance = $client->getAccountBalance();

echo "Total: " . $balance->totalBalanceFormatted . "\n";
echo "Renders remaining: " . $balance->totalBalanceCents . "\n";
echo "\nBreakdown:\n";
echo "  Paid: " . $balance->breakdown->paid->formatted . " (" . $balance->breakdown->paid->cents . " renders)\n";
echo "  Free: " . $balance->breakdown->free->formatted . " (" . $balance->breakdown->free->cents . " renders)\n";
echo "  Bonus: " . $balance->breakdown->bonus->formatted . " (" . $balance->breakdown->bonus->cents . " renders)\n";
```

## API Reference

### `new Browser7Client(string $apiKey, ?string $baseUrl = null)`

Create a new Browser7 client.

**Parameters:**
- `$apiKey` (string, required): Your Browser7 API key
- `$baseUrl` (string, optional): Full API base URL. Defaults to production API.

**Example:**
```php
// Production (default)
$client = new Browser7Client('your-api-key');

// Canadian endpoint
$client = new Browser7Client(
    'your-api-key',
    'https://ca-api.browser7.com/v1'
);
```

### `$client->render(string $url, array $options = []): RenderResult`

Render a URL and poll for the result.

**Parameters:**
- `$url` (string): The URL to render
- `$options` (array, optional):
  - `countryCode` (string): Country code (e.g., 'US', 'GB', 'DE')
  - `city` (string): City name (e.g., 'new.york', 'london')
  - `waitFor` (array): List of wait actions (max 10)
  - `captcha` (string): CAPTCHA mode: 'disabled', 'auto', 'recaptcha_v2', 'recaptcha_v3', 'turnstile'
  - `blockImages` (bool): Block images for faster rendering (default: true)
  - `fetchUrls` (array): Additional URLs to fetch (max 10)

**Returns:** `RenderResult` object

### `$client->getAccountBalance(): AccountBalance`

Get the current account balance.

**Returns:** `AccountBalance` object

**Example:**
```php
$balance = $client->getAccountBalance();
echo "Total: " . $balance->totalBalanceFormatted . "\n";
echo "Renders remaining: " . $balance->totalBalanceCents . "\n";
```

**AccountBalance properties:**
- `$totalBalanceCents` (int): Total balance in cents (also equals renders remaining, since 1 cent = 1 render)
- `$totalBalanceFormatted` (string): Total balance formatted as USD currency (e.g., "$13.00")
- `$breakdown` (object): Balance breakdown by type
  - `$breakdown->paid` - Paid balance with `cents` and `formatted` properties
  - `$breakdown->free` - Free balance with `cents` and `formatted` properties
  - `$breakdown->bonus` - Bonus balance with `cents` and `formatted` properties

## Helper Methods

### `WaitAction::delay(int $duration): array`

Create a delay wait action.

```php
WaitAction::delay(3000);  // Wait 3 seconds
```

### `WaitAction::selector(string $selector, string $state = 'visible', int $timeout = 30000): array`

Create a selector wait action.

```php
WaitAction::selector('.main-content', 'visible', 10000);
```

### `WaitAction::text(string $text, ?string $selector = null, int $timeout = 30000): array`

Create a text wait action.

```php
WaitAction::text('In Stock', '.availability', 10000);
```

### `WaitAction::click(string $selector, int $timeout = 30000): array`

Create a click wait action.

```php
WaitAction::click('.cookie-accept', 5000);
```

## Supported Countries

AT, BE, CA, CH, CZ, DE, FR, GB, HR, HU, IT, NL, PL, SK, US

See [Browser7 Documentation](https://docs.browser7.com) for available cities per country.

## CAPTCHA Support

Browser7 supports automatic CAPTCHA detection and solving for:

- **reCAPTCHA v2** - Google's image-based CAPTCHA
- **reCAPTCHA v3** - Google's score-based CAPTCHA
- **Cloudflare Turnstile** - Cloudflare's CAPTCHA alternative

**Modes:**
- `'disabled'` (default) - Skip CAPTCHA detection (fastest)
- `'auto'` - Auto-detect and solve any CAPTCHA type
- `'recaptcha_v2'`, `'recaptcha_v3'`, `'turnstile'` - Solve specific type

## Contributing

Issues and pull requests are welcome! Please visit our [GitHub repository](https://github.com/browser7data/browser7-php).

## License

MIT

## Support

- 📧 Email: support@browser7.com
- 📚 Documentation: https://docs.browser7.com
- 🐛 Issues: https://github.com/browser7data/browser7-php/issues

## Links

- [Browser7 Website](https://browser7.com)
- [API Documentation](https://docs.browser7.com/api/overview)
- [Dashboard](https://dashboard.browser7.com)
- [Pricing](https://browser7.com/pricing)
