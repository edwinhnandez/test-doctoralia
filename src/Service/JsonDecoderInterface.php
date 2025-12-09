<?php

declare(strict_types=1);

namespace App\Service;

use JsonException;

/**
 * Interface for JSON decoding operations.
 * This abstraction allows for easy testing and swapping of JSON implementations.
 */
interface JsonDecoderInterface
{
    /**
     * Decodes a JSON string into a PHP value.
     *
     * @param string|false $json The JSON string to decode, or false if unavailable
     * @return mixed The decoded value
     * @throws JsonException If the JSON cannot be decoded
     */
    public function decode(string|false $json): mixed;
}

