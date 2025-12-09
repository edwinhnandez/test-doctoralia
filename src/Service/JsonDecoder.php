<?php

declare(strict_types=1);

namespace App\Service;

use JsonException;

/**
 * JSON decoder implementation using PHP's built-in json_decode.
 * This implementation uses JSON_THROW_ON_ERROR flag to ensure exceptions
 * are thrown for invalid JSON.
 */
final class JsonDecoder implements JsonDecoderInterface
{
    /**
     * {@inheritDoc}
     */
    public function decode(string|false $json): mixed
    {
        return json_decode(
            json: false === $json ? '' : $json,
            associative: true,
            depth: 16,
            flags: JSON_THROW_ON_ERROR,
        );
    }
}

