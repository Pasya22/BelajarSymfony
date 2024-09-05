<?php

namespace App\Repository;

use App\Entity\Dummy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dummy>
 *
 * @method Dummy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dummy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dummy[]    findAll()
 * @method Dummy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DummyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dummy::class);
    }

//    /**
//     * @return Dummy[] Returns an array of Dummy objects
//     */
    public function findAllOrderedByCreatedAt()
    {
        return $this->createQueryBuilder('d')
            ->orderBy('d.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findOneByName(string $name): ?Dummy
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
