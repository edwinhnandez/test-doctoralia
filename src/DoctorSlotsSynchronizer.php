<?php

declare(strict_types=1);

namespace App;

use App\Entity\Doctor;
use App\Repository\DoctorRepositoryInterface;
use App\Repository\SlotRepositoryInterface;
use App\Service\ErrorReporterInterface;
use App\Service\NameNormalizerInterface;
use App\Service\SlotParserInterface;
use App\Service\VendorApiClientInterface;
use JsonException;

/**
 * Doctor Slots Synchronizer
 *
 * This class orchestrates the synchronization of doctor data and their appointment slots
 * from an external vendor API to the local database.
 *
 * Responsibilities:
 * - Orchestrating the synchronization workflow
 * - Managing doctor entity lifecycle (create/update)
 * - Handling errors during slot fetching
 * - Coordinating between various services
 *
 * Design decisions:
 * - Single Responsibility: This class only orchestrates the synchronization process
 * - Dependency Injection: All dependencies are injected via constructor
 * - Open/Closed: Can be extended without modification through interfaces
 * - Dependency Inversion: Depends on abstractions (interfaces) not concrete implementations
 */
class DoctorSlotsSynchronizer
{
    public function __construct(
        private readonly VendorApiClientInterface $vendorApiClient,
        private readonly DoctorRepositoryInterface $doctorRepository,
        private readonly SlotRepositoryInterface $slotRepository,
        private readonly NameNormalizerInterface $nameNormalizer,
        private readonly SlotParserInterface $slotParser,
        private readonly ErrorReporterInterface $errorReporter,
    ) {
    }

    /**
     * Synchronizes doctor data and their appointment slots from the vendor API.
     *
     * Business logic flow:
     * 1. Fetch all doctors from vendor API
     * 2. For each doctor:
     *    a. Normalize and update doctor name
     *    b. Clear any previous error flags
     *    c. Fetch doctor's slots from vendor API
     *    d. Parse and persist slots
     *    e. Mark doctor with error if slot fetching fails
     *
     * @throws JsonException If the doctors list cannot be decoded
     */
    public function synchronizeDoctorSlots(): void
    {
        $doctors = $this->vendorApiClient->getDoctors();

        foreach ($doctors as $doctor) {
            $this->synchronizeDoctor($doctor);
        }
    }

    /**
     * Synchronizes a single doctor and their slots.
     *
     * @param array{id: int, name: string} $doctorData Raw doctor data from API
     * @return void
     */
    private function synchronizeDoctor(array $doctorData): void
    {
        $doctorId = (string) $doctorData['id'];
        $normalizedName = $this->nameNormalizer->normalize($doctorData['name']);

        $doctor = $this->doctorRepository->findById($doctorId)
            ?? new Doctor($doctorId, $normalizedName);

        $doctor->setName($normalizedName);
        $doctor->clearError();
        $this->doctorRepository->save($doctor);

        $this->synchronizeDoctorSlots($doctorData['id'], $doctor);
    }

    /**
     * Synchronizes slots for a specific doctor.
     *
     * @param int $doctorId The doctor ID
     * @param Doctor $doctor The doctor entity
     * @return void
     */
    private function synchronizeDoctorSlots(int $doctorId, Doctor $doctor): void
    {
        try {
            $slotsData = $this->vendorApiClient->getDoctorSlots($doctorId);
            $this->processSlots($slotsData, $doctorId);
        } catch (JsonException $e) {
            $this->handleSlotFetchError($doctorId, $doctor);
        }
    }

    /**
     * Processes and persists slots for a doctor.
     *
     * @param array<int, array{start: string, end: string}> $slotsData Raw slot data from API
     * @param int $doctorId The doctor ID
     * @return void
     */
    private function processSlots(array $slotsData, int $doctorId): void
    {
        $findExistingSlot = fn(int $id, \DateTime $start) => $this->slotRepository->findByDoctorAndStart($id, $start);

        foreach ($this->slotParser->parse($slotsData, $doctorId, $findExistingSlot) as $slot) {
            $this->slotRepository->save($slot);
        }
    }

    /**
     * Handles errors that occur during slot fetching.
     *
     * @param int $doctorId The doctor ID
     * @param Doctor $doctor The doctor entity
     * @return void
     */
    private function handleSlotFetchError(int $doctorId, Doctor $doctor): void
    {
        $doctor->markError();
        $this->doctorRepository->save($doctor);
        $this->errorReporter->report($doctorId, 'Error fetching slots for doctor');
    }
}
