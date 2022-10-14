<?php

namespace App\Repository;

use App\Entity\EquipmentType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EquipmentType|null find($id, $lockMode = null, $lockVersion = null)
 * @method EquipmentType|null findOneBy(array $criteria, array $orderBy = null)
 * @method EquipmentType[]    findAll()
 * @method EquipmentType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipmentTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EquipmentType::class);
    }

    // /**
    //  * @return EquipmentType[] Returns an array of EquipmentType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EquipmentType
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
