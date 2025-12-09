<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Interface for error reporting operations.
 * This abstraction allows for easy testing and swapping of error reporting logic.
 */
interface ErrorReporterInterface
{
    /**
     * Determines whether errors should be reported based on business rules.
     *
     * @return bool True if errors should be reported, false otherwise
     */
    public function shouldReport(): bool;

    /**
     * Reports an error for a doctor.
     *
     * @param int $doctorId The ID of the doctor with the error
     * @param string $message Error message
     * @param array $context Additional context data
     * @return void
     */
    public function report(int $doctorId, string $message, array $context = []): void;
}

