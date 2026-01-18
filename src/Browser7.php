<?php

namespace Browser7;

use RuntimeException;

/**
 * Browser7 API Client
 *
 * Official PHP client for the Browser7 web scraping and rendering API.
 *
 * ⚠️ ALPHA RELEASE - This is a pre-release version published to reserve the package name.
 * The Browser7 API is not yet publicly available. Expected launch: Q2 2026.
 *
 * @package Browser7
 * @version 0.1.0-alpha1
 * @link https://browser7.com
 */
class Browser7
{
    /**
     * @var string Browser7 API key
     */
    private string $apiKey;

    /**
     * @var string API base URL
     */
    private string $baseUrl;

    /**
     * Create a new Browser7 client.
     *
     * @param string $apiKey Your Browser7 API key
     * @param string|null $baseUrl Optional custom API base URL
     * @throws RuntimeException If API key is not provided
     */
    public function __construct(string $apiKey, ?string $baseUrl = null)
    {
        if (empty($apiKey)) {
            throw new RuntimeException('API key is required');
        }

        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl ?? 'https://api.browser7.com/v1';
    }

    /**
     * Render a URL and poll for the result.
     *
     * ⚠️ ALPHA: This method is not yet functional. The Browser7 API is not live.
     *
     * @param string $url The URL to render
     * @param array $options Render options
     *   - countryCode: Country code (e.g., 'US', 'GB', 'DE')
     *   - city: City name (e.g., 'new.york', 'london')
     *   - waitFor: Array of wait actions (max 10)
     *   - captcha: CAPTCHA mode ('disabled', 'auto', 'recaptcha_v2', 'recaptcha_v3', 'turnstile')
     *   - blockImages: Whether to block images (default: true)
     *   - fetchUrls: Additional URLs to fetch (max 10)
     * @return RenderResult
     * @throws RuntimeException API is not yet available
     */
    public function render(string $url, array $options = []): RenderResult
    {
        throw new RuntimeException(
            'Browser7 API is not yet publicly available. ' .
            'Expected launch: Q2 2026. Visit https://browser7.com for updates.'
        );
    }

    /**
     * Create a render job (low-level API).
     *
     * ⚠️ ALPHA: This method is not yet functional. The Browser7 API is not live.
     *
     * @param string $url The URL to render
     * @param array $options Render options
     * @return array Response with renderId
     * @throws RuntimeException API is not yet available
     */
    public function createRender(string $url, array $options = []): array
    {
        throw new RuntimeException(
            'Browser7 API is not yet publicly available. ' .
            'Expected launch: Q2 2026. Visit https://browser7.com for updates.'
        );
    }

    /**
     * Get the status and result of a render job (low-level API).
     *
     * ⚠️ ALPHA: This method is not yet functional. The Browser7 API is not live.
     *
     * @param string $renderId The render ID to retrieve
     * @return RenderResult
     * @throws RuntimeException API is not yet available
     */
    public function getRender(string $renderId): RenderResult
    {
        throw new RuntimeException(
            'Browser7 API is not yet publicly available. ' .
            'Expected launch: Q2 2026. Visit https://browser7.com for updates.'
        );
    }
}
