<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\Slot;
use App\Service\SlotParser;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for SlotParser service.
 */
final class SlotParserTest extends TestCase
{
    private SlotParser $parser;

    protected function setUp(): void
    {
        $this->parser = new SlotParser();
    }

    public function testParseCreatesNewSlots(): void
    {
        $slotsData = [
            ['start' => '2024-01-01 10:00:00', 'end' => '2024-01-01 10:30:00'],
            ['start' => '2024-01-01 11:00:00', 'end' => '2024-01-01 11:30:00'],
        ];

        $findExistingSlot = fn(int $doctorId, DateTime $start) => null;

        $result = iterator_to_array($this->parser->parse($slotsData, 1, $findExistingSlot));

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Slot::class, $result[0]);
        $this->assertInstanceOf(Slot::class, $result[1]);
        $this->assertSame('2024-01-01 10:00:00', $result[0]->getStart()->format('Y-m-d H:i:s'));
    }

    public function testParseReusesExistingSlot(): void
    {
        $slotsData = [
            ['start' => '2024-01-01 10:00:00', 'end' => '2024-01-01 10:30:00'],
        ];

        $existingSlot = new Slot(1, new DateTime('2024-01-01 10:00:00'), new DateTime('2024-01-01 10:30:00'));
        $findExistingSlot = fn(int $doctorId, DateTime $start) => $existingSlot;

        $result = iterator_to_array($this->parser->parse($slotsData, 1, $findExistingSlot));

        $this->assertCount(1, $result);
        $this->assertSame($existingSlot, $result[0]);
    }

    public function testParseUpdatesStaleSlot(): void
    {
        $slotsData = [
            ['start' => '2024-01-01 10:00:00', 'end' => '2024-01-01 10:45:00'],
        ];

        $oldEnd = new DateTime('2024-01-01 10:30:00');
        $existingSlot = new Slot(1, new DateTime('2024-01-01 10:00:00'), $oldEnd);
        
        // Make slot stale by manipulating createdAt (using reflection for testing)
        $reflection = new \ReflectionClass($existingSlot);
        $createdAtProperty = $reflection->getProperty('createdAt');
        $createdAtProperty->setAccessible(true);
        $createdAtProperty->setValue($existingSlot, new DateTime('10 minutes ago'));

        $findExistingSlot = fn(int $doctorId, DateTime $start) => $existingSlot;

        $result = iterator_to_array($this->parser->parse($slotsData, 1, $findExistingSlot));

        $this->assertCount(1, $result);
        $this->assertSame($existingSlot, $result[0]);
        // Verify end time was updated for stale slot
        $this->assertSame('2024-01-01 10:45:00', $result[0]->getEnd()->format('Y-m-d H:i:s'));
    }

    public function testParseEmptyArray(): void
    {
        $findExistingSlot = fn(int $doctorId, DateTime $start) => null;

        $result = iterator_to_array($this->parser->parse([], 1, $findExistingSlot));

        $this->assertCount(0, $result);
    }
}

