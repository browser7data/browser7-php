<?php

namespace Browser7;

/**
 * Raised for failed renders (422) and render polling failures/timeouts.
 */
class RenderError extends Browser7Error
{
    private ?string $errorCode;
    private ?string $renderId;
    private ?bool $billable;

    public function __construct(
        string $message,
        ?int $httpStatusCode = null,
        ?array $responseBody = null,
        ?string $errorCode = null,
        ?string $renderId = null,
        ?bool $billable = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $httpStatusCode, $responseBody, $previous);
        $this->errorCode = $errorCode;
        $this->renderId = $renderId;
        $this->billable = $billable;
    }

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getRenderId(): ?string
    {
        return $this->renderId;
    }

    public function getBillable(): ?bool
    {
        return $this->billable;
    }
}
