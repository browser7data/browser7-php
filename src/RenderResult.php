<?php

namespace Browser7;

/**
 * Result from a render operation.
 *
 * @package Browser7
 */
class RenderResult
{
    /**
     * @var string Render status ('completed', 'processing', 'failed')
     */
    public string $status;

    /**
     * @var string|null Rendered HTML content (automatically decompressed)
     */
    public ?string $html = null;

    /**
     * @var string|null Base64-encoded screenshot image (if includeScreenshot was true)
     */
    public ?string $screenshot = null;

    /**
     * @var string|null Load strategy used
     */
    public ?string $loadStrategy = null;

    /**
     * @var array|null City information
     */
    public ?array $selectedCity = null;

    /**
     * @var array|null Network bandwidth statistics
     */
    public ?array $bandwidthMetrics = null;

    /**
     * @var array|null CAPTCHA detection info
     */
    public ?array $captcha = null;

    /**
     * @var array|null Performance timing breakdown
     */
    public ?array $timingBreakdown = null;

    /**
     * @var array|null Additional fetch responses (automatically decompressed)
     */
    public ?array $fetchResponses = null;

    /**
     * @var int Suggested retry interval in seconds
     */
    public int $retryAfter = 1;

    /**
     * @var string|null Error message if status is 'failed'
     */
    public ?string $error = null;

    /**
     * Create a RenderResult from API response data.
     *
     * @param array $data API response data
     */
    public function __construct(array $data)
    {
        $this->status = $data['status'] ?? 'unknown';
        $this->html = $data['html'] ?? null;
        $this->screenshot = $data['screenshot'] ?? null;
        $this->loadStrategy = $data['loadStrategy'] ?? null;
        $this->selectedCity = $data['selectedCity'] ?? null;
        $this->bandwidthMetrics = $data['bandwidthMetrics'] ?? null;
        $this->captcha = $data['captcha'] ?? null;
        $this->timingBreakdown = $data['timingBreakdown'] ?? null;
        $this->fetchResponses = $data['fetchResponses'] ?? null;
        $this->retryAfter = $data['retryAfter'] ?? 1;
        $this->error = $data['error'] ?? null;
    }
}
