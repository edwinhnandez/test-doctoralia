<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Slot;
use DateTime;

/**
 * Slot parser implementation.
 * Converts raw slot data from API into Slot entities, handling updates
 * to existing slots when they become stale.
 */
final class SlotParser implements SlotParserInterface
{
    /**
     * {@inheritDoc}
     *
     * Business logic:
     * - Creates new Slot entities for each slot in the API response
     * - If a slot already exists (found via callback), reuses it
     * - Updates the end time of stale slots (slots older than 5 minutes)
     */
    public function parse(array $slots, int $doctorId, callable $findExistingSlot): iterable
    {
        foreach ($slots as $slot) {
            $start = new DateTime($slot['start']);
            $end = new DateTime($slot['end']);

            /** @var Slot|null $entity */
            $entity = $findExistingSlot($doctorId, $start);

            if (null === $entity) {
                $entity = new Slot($doctorId, $start, $end);
            } elseif ($entity->isStale()) {
                // Update end time for stale slots
                $entity->setEnd($end);
            }

            yield $entity;
        }
    }
}

