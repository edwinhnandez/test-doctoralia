<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Name normalizer implementation.
 * Handles capitalization of doctor names with special handling for names
 * starting with "O'" (e.g., O'Connor, O'Brien).
 *
 * Business rule: Names starting with "O'" should have proper capitalization
 * for both the prefix and the following letter.
 */
final class NameNormalizer implements NameNormalizerInterface
{
    /**
     * {@inheritDoc}
     *
     * Special handling for surnames starting with "O'":
     * - Uses ucwords with space and apostrophe as delimiters
     * - This ensures proper capitalization like "O'Connor" instead of "O'connor"
     *
     * @see https://www.youtube.com/watch?v=PUhU3qCf0Nk Reference for O' name handling
     */
    public function normalize(string $fullName): string
    {
        $parts = explode(' ', $fullName);
        $surname = $parts[1] ?? '';

        // Special handling for surnames starting with "O'"
        if (0 === stripos($surname, "o'")) {
            return ucwords($fullName, ' \'');
        }

        return ucwords($fullName);
    }
}

