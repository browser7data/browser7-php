<?php

namespace Browser7;

/**
 * Result from a render operation.
 *
 * ⚠️ ALPHA: Placeholder class. Full implementation coming with API launch.
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
     * @var string|null Rendered HTML content
     */
    public ?string $html = null;

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
     * @var array|null Additional fetch responses
     */
    public ?array $fetchResponses = null;

    /**
     * Create a RenderResult from API response data.
     *
     * @param array $data API response data
     */
    public function __construct(array $data)
    {
        $this->status = $data['status'] ?? 'unknown';
        $this->html = $data['html'] ?? null;
        $this->selectedCity = $data['selectedCity'] ?? null;
        $this->bandwidthMetrics = $data['bandwidthMetrics'] ?? null;
        $this->captcha = $data['captcha'] ?? null;
        $this->timingBreakdown = $data['timingBreakdown'] ?? null;
        $this->fetchResponses = $data['fetchResponses'] ?? null;
    }
}
