<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }
    /**
     * Selection des dates
     */
    public function getDates()
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            'SELECT DISTINCT  t.endDate
            FROM App\Entity\Task t
            ORDER BY t.endDate DESC'
        );

        return $query->getResult();
    }
    /**
     * TASK par ordre de date en DQL
     */
    public function getTaskOrderedByDateAscDql($date)
    {
        $em = $this->getEntityManager();
        if ($date){

            $dateA = $date.'-01-01';
            $dateB = $date.'-12-31';
            
            $query = $em->createQuery(
                'SELECT t
            FROM App\Entity\Task t
            WHERE ( t.endDate >= :dateA AND  t.endDate <= :dateB )
          
           
            ORDER BY t.startDate ASC'
            )->setParameters([
                'dateA' => $dateA,
                'dateB' => $dateB
            
            ]);
            
        }
            else{

                $query = $em->createQuery(
                    'SELECT t
                    FROM App\Entity\Task t
                    ORDER BY t.startDate ASC'
            );
        }

        // returns an array of Tasks objects
        return $query->getResult();
    }

    /**
     * TASK par ordre de date en QB
     */
    public function getTaskOrderedByDateAscQb($search = null)
    {
        // Requête de base
        $qb = $this->createQueryBuilder('t')
            ->orderBy('t.startDate', 'ASC')
        ;

        // Si recherche en plus
        if ($search !== null) {
            $qb->where('t.startDate LIKE :search')
            ->setParameter('search', '%'.$search.'%');
        }

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Task[] Returns an array of Task objects
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

    // PARTIE STATISTIQUES
    ///stats/****/show
    public function findByTaskByStatusAndDate($status, $dateA, $dateB)
    {
        return $this->createQueryBuilder('t')

            ->where('t.startDate BETWEEN :dateA AND :dateB')
            ->andWhere('t.status = :status')
            ->setParameter('status', $status)
            ->setParameter('dateA', $dateA)
            ->setParameter('dateB', $dateB)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * -- Selection des taches terminées (stats)
     */
    public function getTasksForStats($dateA, $dateB)
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            " SELECT
            p.name, tc.name as taskCat, t.stream, t.startDate, t.startHour, t.endDate , t.endHour, a.department

           FROM App\Entity\Task t
           
           JOIN App\Entity\TaskCategory tc
             WITH t.category = tc.id
           
           JOIN App\Entity\Project p
            WITH t.project = p.id
           
           JOIN App\Entity\Applicant a
            WITH p.requestBy = a.id

           WHERE t.endDate >= '$dateA' AND t.endDate <= '$dateB'

           AND t.status = 'Faite'

           ORDER BY t.startDate"
        );

        return $query->getResult();
    }

}
