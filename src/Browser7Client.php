<?php

namespace Browser7;

/**
 * Browser7 API Client
 *
 * Official PHP client for the Browser7 web scraping and rendering API.
 *
 * @package Browser7
 * @version 1.0.0
 * @link https://browser7.com
 */
class Browser7Client
{
    private const VERSION = '1.0.0';
    private const USER_AGENT = 'browser7-php/' . self::VERSION;

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
            throw new Browser7Error('API key is required');
        }

        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl ?? 'https://api.browser7.com/v1';
    }

    /**
     * Render a URL and poll for the result.
     *
     * @param string $url The URL to render
     * @param array $options Render options
     *   - countryCode: Country code (e.g., 'US', 'GB', 'DE')
     *   - city: City name (e.g., 'new.york', 'london')
     *   - waitFor: Array of wait actions (max 10)
     *   - captcha: CAPTCHA mode ('disabled', 'auto', 'recaptcha_v2', 'recaptcha_v3', 'turnstile')
     *   - blockImages: Whether to block images (default: true)
     *   - fetchUrls: Additional URLs to fetch (max 10)
     *   - includeScreenshot: Enable screenshot capture (default: false)
     *   - screenshotFormat: Screenshot format ('jpeg' or 'png', default: 'jpeg')
     *   - screenshotQuality: JPEG quality 1-100 (default: 80)
     *   - screenshotFullPage: Capture full page or viewport only (default: false)
     *   - debug: Enable debug mode for this render: syncs HTML, fetch responses, and screenshots to dashboard for 7 days (default: false)
     *   - forceNewProxy: Force a new proxy session with a fresh IP address instead of reusing an existing session (default: false)
     *   - onProgress: Optional callback function for progress updates
     * @return RenderResult
     * @throws RuntimeException If render fails or times out
     */
    public function render(string $url, array $options = []): RenderResult
    {
        $maxAttempts = 60;
        $onProgress = $options['onProgress'] ?? null;

        // Remove onProgress from options before sending to API
        unset($options['onProgress']);

        // Create render
        $response = $this->createRender($url, $options);
        $renderId = $response['renderId'];

        // Emit started event
        if (is_callable($onProgress)) {
            $onProgress([
                'type' => 'started',
                'renderId' => $renderId,
                'timestamp' => date('c')
            ]);
        }

        // Wait 2 seconds before starting to poll
        sleep(2);

        // Poll for the result
        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $result = $this->getRender($renderId);

            // Emit polling event
            if (is_callable($onProgress)) {
                $onProgress([
                    'type' => 'polling',
                    'renderId' => $renderId,
                    'timestamp' => date('c'),
                    'status' => $result->status,
                    'attempt' => $attempt + 1,
                    'retryAfter' => $result->retryAfter
                ]);
            }

            if ($result->status === 'completed') {
                // Emit completed event
                if (is_callable($onProgress)) {
                    $onProgress([
                        'type' => 'completed',
                        'renderId' => $renderId,
                        'timestamp' => date('c'),
                        'status' => $result->status
                    ]);
                }

                return $result;
            }

            if ($result->status === 'failed') {
                // Emit failed event
                if (is_callable($onProgress)) {
                    $onProgress([
                        'type' => 'failed',
                        'renderId' => $renderId,
                        'timestamp' => date('c'),
                        'status' => $result->status
                    ]);
                }
                $errorMsg = $result->error ?? 'Unknown error';
                throw new RenderError(
                    "Render failed: {$errorMsg}",
                    null, null,
                    $result->errorCode ?? null, $renderId, $result->billable ?? null
                );
            }

            // Wait before polling again - use server-suggested interval or default to 1 second
            sleep($result->retryAfter);
        }

        throw new RenderError(
            "Render timed out after {$maxAttempts} attempts",
            null, null, 'RENDER_TIMEOUT', $renderId, null
        );
    }

    /**
     * Create a render job (low-level API).
     *
     * @param string $url The URL to render
     * @param array $options Render options (countryCode, city, waitFor, captcha, blockImages, fetchUrls, includeScreenshot, screenshotFormat, screenshotQuality, screenshotFullPage, debug, forceNewProxy)
     * @return array Response with renderId
     * @throws RuntimeException If the request fails
     */
    public function createRender(string $url, array $options = []): array
    {
        // Build request payload with only defined API options
        $payload = ['url' => $url];

        if (isset($options['countryCode'])) {
            $payload['countryCode'] = $options['countryCode'];
        }
        if (isset($options['city'])) {
            $payload['city'] = $options['city'];
        }
        if (isset($options['fetchUrls'])) {
            $payload['fetchUrls'] = $options['fetchUrls'];
        }
        if (isset($options['waitFor'])) {
            $payload['waitFor'] = $options['waitFor'];
        }
        if (isset($options['captcha'])) {
            $payload['captcha'] = $options['captcha'];
        }
        if (isset($options['blockImages'])) {
            $payload['blockImages'] = $options['blockImages'];
        }
        if (isset($options['includeScreenshot'])) {
            $payload['includeScreenshot'] = $options['includeScreenshot'];
        }
        if (isset($options['screenshotFormat'])) {
            $payload['screenshotFormat'] = $options['screenshotFormat'];
        }
        if (isset($options['screenshotQuality'])) {
            $payload['screenshotQuality'] = $options['screenshotQuality'];
        }
        if (isset($options['screenshotFullPage'])) {
            $payload['screenshotFullPage'] = $options['screenshotFullPage'];
        }
        if (isset($options['debug'])) {
            $payload['debug'] = $options['debug'];
        }
        if (isset($options['forceNewProxy'])) {
            $payload['forceNewProxy'] = $options['forceNewProxy'];
        }

        $renderUrl = "{$this->baseUrl}/renders";

        $ch = curl_init($renderUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->apiKey}",
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Browser7Error("Failed to connect to {$renderUrl}: {$error}");
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            self::throwApiError($httpCode, $response, 'Failed to start render');
        }

        return json_decode($response, true);
    }

    /**
     * Get the status and result of a render job (low-level API).
     *
     * @param string $renderId The render ID to retrieve
     * @return RenderResult
     * @throws RuntimeException If the request fails
     */
    public function getRender(string $renderId): RenderResult
    {
        $statusUrl = "{$this->baseUrl}/renders/{$renderId}";

        $ch = curl_init($statusUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Browser7Error("Failed to connect to {$statusUrl}: {$error}");
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            self::throwApiError($httpCode, $response, 'Failed to get render status');
        }

        $result = json_decode($response, true);

        // Decompress the gzipped HTML if present
        if (isset($result['html'])) {
            try {
                $decoded = base64_decode($result['html']);
                $decompressed = gzdecode($decoded);
                if ($decompressed !== false) {
                    $result['html'] = $decompressed;
                }
            } catch (\Exception $e) {
                // Silently fail decompression
            }
        }

        // Decompress and parse fetchResponses if present
        if (isset($result['fetchResponses'])) {
            try {
                $decoded = base64_decode($result['fetchResponses']);
                $decompressed = gzdecode($decoded);
                if ($decompressed !== false) {
                    $result['fetchResponses'] = json_decode($decompressed, true);
                }
            } catch (\Exception $e) {
                // Silently fail decompression/parsing
            }
        }

        return new RenderResult($result);
    }

    /**
     * Get the current account balance.
     *
     * @return AccountBalance
     * @throws RuntimeException If the request fails
     */
    public function getAccountBalance(): AccountBalance
    {
        $balanceUrl = "{$this->baseUrl}/account/balance";

        $ch = curl_init($balanceUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$this->apiKey}"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Browser7Error("Failed to connect to {$balanceUrl}: {$error}");
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            self::throwApiError($httpCode, $response, 'Failed to get account balance');
        }

        return new AccountBalance(json_decode($response, true));
    }

    /**
     * Get available API regions.
     *
     * This is a public endpoint — no API key is required, but the SDK
     * makes the call for you with a consistent interface.
     *
     * @return Region[]
     * @throws RuntimeException If the request fails
     */
    public function getRegions(): array
    {
        $regionsUrl = "{$this->baseUrl}/regions";

        $ch = curl_init($regionsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, self::USER_AGENT);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Browser7Error("Failed to connect to {$regionsUrl}: {$error}");
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            self::throwApiError($httpCode, $response, 'Failed to get regions');
        }

        $data = json_decode($response, true);

        return array_map(
            fn(array $r) => new Region($r),
            $data['regions'] ?? []
        );
    }

    /**
     * Parse response and throw the appropriate typed error.
     *
     * @throws Browser7Error|AuthenticationError|ValidationError|RateLimitError|InsufficientBalanceError|RenderError
     */
    private static function throwApiError(int $httpCode, string $responseBody, string $context): never
    {
        $body = json_decode($responseBody, true);
        $apiMessage = $body['message'] ?? $responseBody;
        $message = "{$context}: {$httpCode} {$apiMessage}";

        match ($httpCode) {
            400 => throw new ValidationError($message, $httpCode, $body),
            401, 403 => throw new AuthenticationError($message, $httpCode, $body),
            402 => throw new InsufficientBalanceError($message, $httpCode, $body),
            422 => throw new RenderError(
                $message, $httpCode, $body,
                $body['errorCode'] ?? null,
                $body['id'] ?? null,
                $body['billable'] ?? null,
            ),
            429 => throw new RateLimitError($message, $httpCode, $body),
            default => throw new Browser7Error($message, $httpCode, $body),
        };
    }
}
