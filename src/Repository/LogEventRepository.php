<?php

namespace App\Repository;

use App\Entity\LogEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LogEvent|null find($id, $lockMode = null, $lockVersion = null)
 * @method LogEvent|null findOneBy(array $criteria, array $orderBy = null)
 * @method LogEvent[]    findAll()
 * @method LogEvent[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LogEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LogEvent::class);
    }


    // /**
    //  * @return LogEvent[] Returns an array of LogEvent objects
    //  */
    public function findLastFive()
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult()
        ;
    }
    public function findLastFifty()
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.id', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult()
        ;
    }

    // /**
    //  * @return LogEvent[] Returns an array of LogEvent objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LogEvent
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
