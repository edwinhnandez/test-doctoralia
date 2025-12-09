<?php

declare(strict_types=1);

namespace App\Tests;

use App\DoctorSlotsSynchronizer;
use App\Entity\Doctor;
use App\Repository\DoctorRepositoryInterface;
use App\Repository\SlotRepositoryInterface;
use App\Service\ErrorReporterInterface;
use App\Service\NameNormalizerInterface;
use App\Service\SlotParserInterface;
use App\Service\VendorApiClientInterface;
use JsonException;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for DoctorSlotsSynchronizer.
 * These tests verify the orchestration logic and business flow.
 */
final class DoctorSlotsSynchronizerTest extends TestCase
{
    private VendorApiClientInterface $vendorApiClient;
    private DoctorRepositoryInterface $doctorRepository;
    private SlotRepositoryInterface $slotRepository;
    private NameNormalizerInterface $nameNormalizer;
    private SlotParserInterface $slotParser;
    private ErrorReporterInterface $errorReporter;
    private DoctorSlotsSynchronizer $synchronizer;

    protected function setUp(): void
    {
        $this->vendorApiClient = $this->createMock(VendorApiClientInterface::class);
        $this->doctorRepository = $this->createMock(DoctorRepositoryInterface::class);
        $this->slotRepository = $this->createMock(SlotRepositoryInterface::class);
        $this->nameNormalizer = $this->createMock(NameNormalizerInterface::class);
        $this->slotParser = $this->createMock(SlotParserInterface::class);
        $this->errorReporter = $this->createMock(ErrorReporterInterface::class);

        $this->synchronizer = new DoctorSlotsSynchronizer(
            $this->vendorApiClient,
            $this->doctorRepository,
            $this->slotRepository,
            $this->nameNormalizer,
            $this->slotParser,
            $this->errorReporter,
        );
    }

    public function testSynchronizeDoctorSlotsCreatesNewDoctor(): void
    {
        $doctorsData = [
            ['id' => 1, 'name' => 'john doe'],
        ];

        $this->vendorApiClient
            ->expects($this->once())
            ->method('getDoctors')
            ->willReturn($doctorsData);

        $this->nameNormalizer
            ->expects($this->once())
            ->method('normalize')
            ->with('john doe')
            ->willReturn('John Doe');

        $this->doctorRepository
            ->expects($this->once())
            ->method('findById')
            ->with('1')
            ->willReturn(null);

        $this->doctorRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Doctor $doctor) {
                return $doctor->getId() === '1' && $doctor->getName() === 'John Doe' && !$doctor->hasError();
            }));

        $this->vendorApiClient
            ->expects($this->once())
            ->method('getDoctorSlots')
            ->with(1)
            ->willReturn([]);

        $this->slotParser
            ->expects($this->once())
            ->method('parse')
            ->willReturn([]);

        $this->synchronizer->synchronizeDoctorSlots();
    }

    public function testSynchronizeDoctorSlotsUpdatesExistingDoctor(): void
    {
        $doctorsData = [
            ['id' => 1, 'name' => 'john doe updated'],
        ];

        $existingDoctor = new Doctor('1', 'John Doe');

        $this->vendorApiClient
            ->method('getDoctors')
            ->willReturn($doctorsData);

        $this->nameNormalizer
            ->method('normalize')
            ->willReturn('John Doe Updated');

        $this->doctorRepository
            ->method('findById')
            ->willReturn($existingDoctor);

        $this->doctorRepository
            ->expects($this->once())
            ->method('save')
            ->with($this->callback(function (Doctor $doctor) {
                return $doctor->getName() === 'John Doe Updated' && !$doctor->hasError();
            }));

        $this->vendorApiClient
            ->method('getDoctorSlots')
            ->willReturn([]);

        $this->slotParser
            ->method('parse')
            ->willReturn([]);

        $this->synchronizer->synchronizeDoctorSlots();
    }

    public function testSynchronizeDoctorSlotsHandlesSlotFetchError(): void
    {
        $doctorsData = [
            ['id' => 1, 'name' => 'john doe'],
        ];

        $doctor = new Doctor('1', 'John Doe');

        $this->vendorApiClient
            ->method('getDoctors')
            ->willReturn($doctorsData);

        $this->nameNormalizer
            ->method('normalize')
            ->willReturn('John Doe');

        $this->doctorRepository
            ->method('findById')
            ->willReturn($doctor);

        $this->doctorRepository
            ->expects($this->exactly(2))
            ->method('save')
            ->with($this->callback(function (Doctor $doctor) use ($doctorsData) {
                // First save clears error, second save marks error
                return true;
            }));

        $this->vendorApiClient
            ->method('getDoctorSlots')
            ->willThrowException(new JsonException());

        $this->errorReporter
            ->expects($this->once())
            ->method('report')
            ->with(1, 'Error fetching slots for doctor');

        $this->synchronizer->synchronizeDoctorSlots();

        $this->assertTrue($doctor->hasError());
    }

    public function testSynchronizeDoctorSlotsProcessesMultipleDoctors(): void
    {
        $doctorsData = [
            ['id' => 1, 'name' => 'john doe'],
            ['id' => 2, 'name' => 'jane smith'],
        ];

        $this->vendorApiClient
            ->method('getDoctors')
            ->willReturn($doctorsData);

        $this->nameNormalizer
            ->method('normalize')
            ->willReturnCallback(fn($name) => ucwords($name));

        $this->doctorRepository
            ->method('findById')
            ->willReturn(null);

        $this->vendorApiClient
            ->expects($this->exactly(2))
            ->method('getDoctorSlots')
            ->willReturn([]);

        $this->slotParser
            ->method('parse')
            ->willReturn([]);

        $this->synchronizer->synchronizeDoctorSlots();
    }

    public function testSynchronizeDoctorSlotsClearsErrorFlag(): void
    {
        $doctorsData = [
            ['id' => 1, 'name' => 'john doe'],
        ];

        $doctor = new Doctor('1', 'John Doe');
        $doctor->markError();

        $this->vendorApiClient
            ->method('getDoctors')
            ->willReturn($doctorsData);

        $this->nameNormalizer
            ->method('normalize')
            ->willReturn('John Doe');

        $this->doctorRepository
            ->method('findById')
            ->willReturn($doctor);

        $this->doctorRepository
            ->expects($this->atLeastOnce())
            ->method('save')
            ->with($this->callback(function (Doctor $doctor) {
                return !$doctor->hasError();
            }));

        $this->vendorApiClient
            ->method('getDoctorSlots')
            ->willReturn([]);

        $this->slotParser
            ->method('parse')
            ->willReturn([]);

        $this->synchronizer->synchronizeDoctorSlots();
    }
}

