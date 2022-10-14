<?php

namespace App\Repository;

use App\Entity\TaskCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TaskCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaskCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaskCategory[]    findAll()
 * @method TaskCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskCategory::class);
    }

    // /**
    //  * @return TaskCategory[] Returns an array of TaskCategory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TaskCategory
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
