<?php

namespace Browser7;

/**
 * Base exception for all Browser7 SDK errors.
 *
 * Extends RuntimeException for backward compatibility with existing catch blocks.
 */
class Browser7Error extends \RuntimeException
{
    protected ?int $httpStatusCode;
    protected ?array $responseBody;

    public function __construct(
        string $message,
        ?int $httpStatusCode = null,
        ?array $responseBody = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->httpStatusCode = $httpStatusCode;
        $this->responseBody = $responseBody;
    }

    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }

    public function getResponseBody(): ?array
    {
        return $this->responseBody;
    }
}
