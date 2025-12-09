<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Interface for HTTP client operations.
 * This abstraction allows for easy testing and swapping of HTTP implementations.
 */
interface HttpClientInterface
{
    /**
     * Fetches data from the given URL with basic authentication.
     *
     * @param string $url The URL to fetch data from
     * @param string $username Basic auth username
     * @param string $password Basic auth password
     * @return string|false The response body as string, or false on failure
     */
    public function fetch(string $url, string $username, string $password): string|false;
}

