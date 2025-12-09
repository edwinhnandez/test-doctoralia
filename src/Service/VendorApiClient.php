<?php

declare(strict_types=1);

namespace App\Service;

use JsonException;

/**
 * Vendor API client implementation.
 * Handles all communication with the external vendor API, including
 * authentication and endpoint management.
 */
final class VendorApiClient implements VendorApiClientInterface
{
    private const ENDPOINT_BASE = 'http://localhost:2137/api/doctors';
    private const USERNAME = 'docplanner';
    private const PASSWORD = 'docplanner';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly JsonDecoderInterface $jsonDecoder,
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getDoctors(): array
    {
        $response = $this->httpClient->fetch(
            self::ENDPOINT_BASE,
            self::USERNAME,
            self::PASSWORD
        );

        return $this->jsonDecoder->decode($response);
    }

    /**
     * {@inheritDoc}
     */
    public function getDoctorSlots(int $doctorId): array
    {
        $url = self::ENDPOINT_BASE . '/' . $doctorId . '/slots';
        $response = $this->httpClient->fetch(
            $url,
            self::USERNAME,
            self::PASSWORD
        );

        return $this->jsonDecoder->decode($response);
    }
}

