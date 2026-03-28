<?php

namespace Browser7;

/**
 * An available API region.
 *
 * @package Browser7
 */
class Region
{
    /**
     * @var string Region code (e.g., 'eu', 'ca', 'sg')
     */
    public string $code;

    /**
     * @var string Human-readable region name (e.g., 'Europe')
     */
    public string $name;

    /**
     * @var string Region status ('active', 'maintenance', 'inactive')
     */
    public string $status;

    /**
     * Create a Region from API response data.
     *
     * @param array $data API response data
     */
    public function __construct(array $data)
    {
        $this->code = $data['code'] ?? '';
        $this->name = $data['name'] ?? '';
        $this->status = $data['status'] ?? '';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return "Region(code={$this->code}, name={$this->name}, status={$this->status})";
    }
}
