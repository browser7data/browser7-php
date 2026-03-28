<?php

namespace Browser7;

/**
 * Raised for 429 Too Many Requests responses.
 */
class RateLimitError extends Browser7Error
{
    /**
     * Get the maximum concurrent requests allowed, parsed from the error message.
     */
    public function getConcurrentLimit(): ?int
    {
        if (preg_match('/Maximum allowed:\s*(\d+)/', $this->getMessage(), $matches)) {
            return (int) $matches[1];
        }
        return null;
    }
}
