<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Doctor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Doctor repository implementation using Doctrine ORM.
 */
final class DoctorRepository implements DoctorRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Doctor::class);
    }

    /**
     * {@inheritDoc}
     */
    public function findById(string $id): ?Doctor
    {
        /** @var Doctor|null */
        return $this->repository->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function save(Doctor $doctor): void
    {
        $entityManager = $this->repository->createQueryBuilder('alias')->getEntityManager();
        $entityManager->persist($doctor);
        $entityManager->flush();
    }
}

