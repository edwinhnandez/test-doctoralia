<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Doctor;
use App\Repository\DoctorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for DoctorRepository.
 * Note: These are integration-style tests that would require Doctrine setup.
 * In a real scenario, you might use a test database or mock the EntityManager more thoroughly.
 */
final class DoctorRepositoryTest extends TestCase
{
    public function testFindByIdReturnsDoctorWhenExists(): void
    {
        $doctor = new Doctor('1', 'John Doe');
        
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with('1')->willReturn($doctor);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->willReturn($repository);

        $doctorRepository = new DoctorRepository($entityManager);
        $result = $doctorRepository->findById('1');

        $this->assertInstanceOf(Doctor::class, $result);
        $this->assertSame('1', $result->getId());
    }

    public function testFindByIdReturnsNullWhenNotExists(): void
    {
        $repository = $this->createMock(EntityRepository::class);
        $repository->method('find')->with('999')->willReturn(null);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->method('getRepository')->willReturn($repository);

        $doctorRepository = new DoctorRepository($entityManager);
        $result = $doctorRepository->findById('999');

        $this->assertNull($result);
    }
}

