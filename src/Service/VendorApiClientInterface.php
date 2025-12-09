<?php

declare(strict_types=1);

namespace App\Service;

use JsonException;

/**
 * Interface for vendor API client operations.
 * This abstraction encapsulates all vendor API interactions, making it easy
 * to test and swap implementations.
 */
interface VendorApiClientInterface
{
    /**
     * Fetches all doctors from the vendor API.
     *
     * @return array<int, array{id: int, name: string}> Array of doctor data
     * @throws JsonException If the response cannot be decoded
     */
    public function getDoctors(): array;

    /**
     * Fetches slots for a specific doctor from the vendor API.
     *
     * @param int $doctorId The ID of the doctor
     * @return array<int, array{start: string, end: string}> Array of slot data
     * @throws JsonException If the response cannot be decoded
     */
    public function getDoctorSlots(int $doctorId): array;
}

