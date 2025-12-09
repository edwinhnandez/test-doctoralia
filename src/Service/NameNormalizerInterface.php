<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Interface for name normalization operations.
 * This abstraction allows for easy testing and swapping of normalization logic.
 */
interface NameNormalizerInterface
{
    /**
     * Normalizes a doctor's full name according to business rules.
     * Handles special cases like names starting with "O'" (e.g., O'Connor).
     *
     * @param string $fullName The full name to normalize
     * @return string The normalized name
     */
    public function normalize(string $fullName): string;
}

