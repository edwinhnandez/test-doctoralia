<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Doctor;

/**
 * Interface for doctor repository operations.
 * This abstraction allows for easy testing and swapping of persistence implementations.
 */
interface DoctorRepositoryInterface
{
    /**
     * Finds a doctor by ID.
     *
     * @param string $id The doctor ID
     * @return Doctor|null The doctor entity or null if not found
     */
    public function findById(string $id): ?Doctor;

    /**
     * Persists a doctor entity.
     *
     * @param Doctor $doctor The doctor entity to persist
     * @return void
     */
    public function save(Doctor $doctor): void;
}

