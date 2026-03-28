<?php

namespace Browser7;

/**
 * Account balance information.
 *
 * ⚠️ ALPHA: Placeholder class. Full implementation coming with API launch.
 *
 * @package Browser7
 */
class AccountBalance
{
    /**
     * @var int Total balance in cents (also equals renders remaining, since 1 cent = 1 render)
     */
    public int $totalBalanceCents;

    /**
     * @var string Total balance formatted as USD currency
     */
    public string $totalBalanceFormatted;

    /**
     * @var object Balance breakdown by type
     */
    public object $breakdown;

    /**
     * Create an AccountBalance from API response data.
     *
     * @param array $data API response data
     */
    public function __construct(array $data)
    {
        $this->totalBalanceCents = $data['totalBalanceCents'] ?? 0;
        $this->totalBalanceFormatted = $data['totalBalanceFormatted'] ?? '$0.00';
        $this->breakdown = (object) ($data['breakdown'] ?? [
            'paid' => ['cents' => 0, 'formatted' => '$0.00'],
            'free' => ['cents' => 0, 'formatted' => '$0.00'],
            'bonus' => ['cents' => 0, 'formatted' => '$0.00']
        ]);

        // Convert nested arrays to objects for easier property access
        foreach ($this->breakdown as $key => $value) {
            if (is_array($value)) {
                $this->breakdown->$key = (object) $value;
            }
        }
    }

    /**
     * Get string representation of balance.
     *
     * @return string
     */
    public function __toString(): string
    {
        return sprintf(
            'AccountBalance(total=%s, renders_remaining=%d)',
            $this->totalBalanceFormatted,
            $this->totalBalanceCents
        );
    }
}
