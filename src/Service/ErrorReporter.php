<?php

declare(strict_types=1);

namespace App\Service;

use DateTime;
use Psr\Log\LoggerInterface;

/**
 * Error reporter implementation.
 * Handles error reporting logic with business rule: errors are not reported on Sundays.
 */
final class ErrorReporter implements ErrorReporterInterface
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * Business rule: Errors are not reported on Sundays.
     * This allows for maintenance windows or reduced monitoring on weekends.
     */
    public function shouldReport(): bool
    {
        return (new DateTime())->format('D') !== 'Sun';
    }

    /**
     * {@inheritDoc}
     */
    public function report(int $doctorId, string $message, array $context = []): void
    {
        if ($this->shouldReport()) {
            $this->logger->info($message, array_merge(['doctorId' => $doctorId], $context));
        }
    }
}

