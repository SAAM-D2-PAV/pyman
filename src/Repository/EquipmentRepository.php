<?php

namespace App\Repository;

use App\Entity\Equipment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Equipment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipment[]    findAll()
 * @method Equipment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipment::class);
    }

    /**
     * @return Equipment[] Returns an array of Equipment objects
    */
    public function missingEquipments()
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.missing = :val')
            ->setParameter('val', 1)
            ->orderBy('e.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Equipment[] Returns an array of Equipment objects
     */
    public function ratedEquipments()
    {
        return $this->createQueryBuilder('e')
            ->where('e.rent is not null')
            ->orderBy('e.id', 'ASC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }
    

    /*
    public function findOneBySomeField($value): ?Equipment
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
