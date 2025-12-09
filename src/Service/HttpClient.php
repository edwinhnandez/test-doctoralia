<?php

declare(strict_types=1);

namespace App\Service;

/**
 * HTTP client implementation using file_get_contents.
 * This implementation uses PHP's built-in file_get_contents with stream context
 * for basic authentication.
 */
final class HttpClient implements HttpClientInterface
{
    /**
     * {@inheritDoc}
     */
    public function fetch(string $url, string $username, string $password): string|false
    {
        $auth = base64_encode(sprintf('%s:%s', $username, $password));

        return @file_get_contents(
            filename: $url,
            context: stream_context_create([
                'http' => [
                    'header' => 'Authorization: Basic ' . $auth,
                ],
            ]),
        );
    }
}

