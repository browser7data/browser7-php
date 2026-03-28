<?php

namespace Browser7;

/**
 * Raised for 400 Bad Request responses (invalid parameters).
 */
class ValidationError extends Browser7Error
{
    /**
     * Get validation error details from the API response.
     */
    public function getDetails(): ?array
    {
        return $this->responseBody['details'] ?? null;
    }
}
