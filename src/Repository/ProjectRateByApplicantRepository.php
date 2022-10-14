<?php

namespace App\Repository;

use App\Entity\ProjectRateByApplicant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProjectRateByApplicant|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProjectRateByApplicant|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProjectRateByApplicant[]    findAll()
 * @method ProjectRateByApplicant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRateByApplicantRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectRateByApplicant::class);
    }

    // /**
    //  * @return ProjectRateByApplicant[] Returns an array of ProjectRateByApplicant objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProjectRateByApplicant
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
