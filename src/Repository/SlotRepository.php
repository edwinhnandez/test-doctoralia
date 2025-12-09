<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Slot;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Slot repository implementation using Doctrine ORM.
 */
final class SlotRepository implements SlotRepositoryInterface
{
    private EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(Slot::class);
    }

    /**
     * {@inheritDoc}
     */
    public function findByDoctorAndStart(int $doctorId, DateTime $start): ?Slot
    {
        /** @var Slot|null */
        return $this->repository->findOneBy([
            'doctorId' => $doctorId,
            'start' => $start,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function save(Slot $slot): void
    {
        $entityManager = $this->repository->createQueryBuilder('alias')->getEntityManager();
        $entityManager->persist($slot);
        $entityManager->flush();
    }
}

