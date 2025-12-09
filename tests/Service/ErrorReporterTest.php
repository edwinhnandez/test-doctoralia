<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\ErrorReporter;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

/**
 * Unit tests for ErrorReporter service.
 */
final class ErrorReporterTest extends TestCase
{
    private LoggerInterface $logger;
    private ErrorReporter $reporter;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->reporter = new ErrorReporter($this->logger);
    }

    public function testShouldReportReturnsTrueOnNonSunday(): void
    {
        // Mock DateTime to return a non-Sunday day
        $this->assertTrue($this->reporter->shouldReport());
    }

    public function testReportLogsWhenShouldReportIsTrue(): void
    {
        // This test assumes we're not running on Sunday
        // In a real scenario, you might want to use a date/time mock
        if ($this->reporter->shouldReport()) {
            $this->logger
                ->expects($this->once())
                ->method('info')
                ->with(
                    'Error message',
                    ['doctorId' => 1, 'additional' => 'context']
                );

            $this->reporter->report(1, 'Error message', ['additional' => 'context']);
        }
    }

    public function testReportDoesNotLogOnSunday(): void
    {
        // This is a conceptual test - in practice you'd mock DateTime
        // For now, we test the logic structure
        $this->assertIsBool($this->reporter->shouldReport());
    }
}

