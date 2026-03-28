<?php

namespace Browser7;

/**
 * Helper class for creating wait actions.
 *
 * @package Browser7
 */
class WaitAction
{
    /**
     * Create a delay wait action.
     *
     * @param int $duration Duration in milliseconds (100-60000)
     * @return array Wait action array
     *
     * @example
     * WaitAction::delay(3000);  // Wait 3 seconds
     */
    public static function delay(int $duration): array
    {
        return [
            'type' => 'delay',
            'duration' => $duration
        ];
    }

    /**
     * Create a selector wait action.
     *
     * @param string $selector CSS selector to wait for
     * @param string $state Element state ('visible', 'hidden', 'attached')
     * @param int $timeout Timeout in milliseconds (1000-60000)
     * @return array Wait action array
     *
     * @example
     * WaitAction::selector('.main-content', 'visible', 10000);
     */
    public static function selector(
        string $selector,
        string $state = 'visible',
        int $timeout = 30000
    ): array {
        return [
            'type' => 'selector',
            'selector' => $selector,
            'state' => $state,
            'timeout' => $timeout
        ];
    }

    /**
     * Create a text wait action.
     *
     * @param string $text Text to wait for
     * @param string|null $selector Optional CSS selector to limit search scope
     * @param int $timeout Timeout in milliseconds (1000-60000)
     * @return array Wait action array
     *
     * @example
     * WaitAction::text('In Stock', '.availability', 10000);
     */
    public static function text(
        string $text,
        ?string $selector = null,
        int $timeout = 30000
    ): array {
        $action = [
            'type' => 'text',
            'text' => $text,
            'timeout' => $timeout
        ];

        if ($selector !== null) {
            $action['selector'] = $selector;
        }

        return $action;
    }

    /**
     * Create a click wait action.
     *
     * @param string $selector CSS selector of element to click
     * @param int $timeout Timeout in milliseconds (1000-60000)
     * @return array Wait action array
     *
     * @example
     * WaitAction::click('.cookie-accept', 5000);
     */
    public static function click(
        string $selector,
        int $timeout = 30000
    ): array {
        return [
            'type' => 'click',
            'selector' => $selector,
            'timeout' => $timeout
        ];
    }
}
