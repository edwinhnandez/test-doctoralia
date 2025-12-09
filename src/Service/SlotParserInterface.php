<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Slot;
use JsonException;

/**
 * Interface for slot parsing operations.
 * This abstraction allows for easy testing and swapping of parsing logic.
 */
interface SlotParserInterface
{
    /**
     * Parses slot data from API response into Slot entities.
     *
     * @param array $slots Raw slot data from API
     * @param int $doctorId The ID of the doctor these slots belong to
     * @param callable $findExistingSlot Callback to find existing slot by doctorId and start time
     * @return iterable<Slot> Generator yielding Slot entities
     */
    public function parse(array $slots, int $doctorId, callable $findExistingSlot): iterable;
}

