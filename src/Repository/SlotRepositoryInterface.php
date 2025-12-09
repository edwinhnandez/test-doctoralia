<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Slot;
use DateTime;

/**
 * Interface for slot repository operations.
 * This abstraction allows for easy testing and swapping of persistence implementations.
 */
interface SlotRepositoryInterface
{
    /**
     * Finds a slot by doctor ID and start time.
     *
     * @param int $doctorId The doctor ID
     * @param DateTime $start The slot start time
     * @return Slot|null The slot entity or null if not found
     */
    public function findByDoctorAndStart(int $doctorId, DateTime $start): ?Slot;

    /**
     * Persists a slot entity.
     *
     * @param Slot $slot The slot entity to persist
     * @return void
     */
    public function save(Slot $slot): void;
}

