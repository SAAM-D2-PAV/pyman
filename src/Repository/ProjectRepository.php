<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Project|null find($id, $lockMode = null, $lockVersion = null)
 * @method Project|null findOneBy(array $criteria, array $orderBy = null)
 * @method Project[]    findAll()
 * @method Project[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }
    /**
     * PROJECT par ordre de date en DQL
     */
    public function getProjectOrderedByDateAscDql()
    {
        $em = $this->getEntityManager();

        $query = $em->createQuery(
            'SELECT p
            FROM App\Entity\Project p
            ORDER BY p.deliveryDate ASC'
        );

        return $query->getResult();
    }
    /**
     * Selection des dates
     */
    public function getDates()
    {
        $status = "Fait";
        $status2 = "Annulé";
        $status3 = "Refusé";

        $em = $this->getEntityManager();

        $query = $em->createQuery(
            'SELECT DISTINCT  p.deliveryDate
            FROM App\Entity\Project p
            WHERE (p.status = :status OR p.status = :status2 OR p.status = :status3)
            ORDER BY p.deliveryDate DESC'
        )->setParameters([
            
            'status' => $status,
            'status2' => $status2,
            'status3' => $status3,
          
        ]);

        return $query->getResult();
    }
    /**
     * PROJECTS à faire et en cours par ordre de date 
     */
    public function getInProgressProjectOrderedByDateAsc()
    {
        $status = "A faire";
        $status2 = "En cours";
        // Requête de base
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.deliveryDate', 'ASC')
        ;

        $qb->where('p.status = :status')->orWhere('p.status = :status2')
        ->setParameters(['status' => $status,
        'status2' => $status2]);
        

        return $qb->getQuery()->getResult();
    }
    // TODO problème avec cette méthode
    //Vérifier les paramètres de dates
    /**
     * PROJECTS faits annulés refusés
     */
    public function getCompletedProjectOrderedByDateAsc($date)
    {
        $status = "Fait";
        $status2 = "Annulé";
        $status3 = "Refusé";

        if ($date){

            $dateA = $date.'-01-01 00:00:00';
            $dateB = $date.'-12-31 00:00:00';
            
            $entityManager = $this->getEntityManager();
            $query = $entityManager->createQuery(
                'SELECT p
            FROM App\Entity\Project p
            WHERE ( p.deliveryDate >= :dateA AND p.deliveryDate <= :dateB )
            AND ( p.status = :status OR p.status = :status2 OR p.status = :status3 )
           
            ORDER BY p.deliveryDate ASC'
            )->setParameters([
                'dateA' => $dateA,
                'dateB' => $dateB,
                'status' => $status,
                'status2' => $status2,
                'status3' => $status3
            ]);
            // returns an array of Product objects
            return $query->getResult();
        }
        else{
            // Requête de base
            $qb = $this->createQueryBuilder('p')
                ->orderBy('p.deliveryDate', 'ASC')
            ;

            $qb->where('p.status = :status')->orWhere('p.status = :status2')->orWhere('p.status = :status3')
                ->setParameters([
                    'status' => $status,
                    'status2' => $status2,
                    'status3' => $status3
                ]);


            return $qb->getQuery()->getResult();
        }


    }
    /**
     * PROJECT par ordre de date en QB
     */
    public function getProjectOrderedByDateAscQb($search = null)
    {
        $status = "A faire";
        $status2 = "En cours";
        // Requête de base
        $qb = $this->createQueryBuilder('p')
            ->orderBy('p.deliveryDate', 'ASC')
        ;

        // Si recherche en plus
        if ($search !== null) {
            $qb->where('p.deliveryDate LIKE :search')
            ->setParameter('search', '%'.$search.'%');
        }
        $qb->where('p.status = :status')->orWhere('p.status = :status2')
        ->setParameters(['status' => $status,
        'status2' => $status2]);

        return $qb->getQuery()->getResult();
    }

    // /**
    //  * @return Project[] Returns an array of Project objects
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
    public function findOneBySomeField($value): ?Project
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    /**
     * methode de recherche de projet par formulaire
     */
    public function findSearch($data)
    {

        $status = "A faire";
        $status2 = "En cours";

        $qb = $this->createQueryBuilder('p');

        $qb->where('p.status = :status')->orWhere('p.status = :status2')
        ->setParameter('status', $status)->setParameter('status2',$status2);

        if ($data["q"] !== null) {

            $qb
            ->andwhere('p.name LIKE :search')
            ->setParameter('search', '%'.$data["q"].'%');

        }
        if ($data["category"] !== null) {

            $cat = $data["category"];
            
            $qb
            ->andWhere('p.category = :cat')
            ->setParameter('cat', $cat);
           

        }
       
        $qb->orderBy('p.deliveryDate', 'ASC');

       

        return $qb->getQuery()->getResult();
    }
   
    public function findProjectByStatusAndDate($status, $dateA, $dateB)
    {

        $em = $this->getEntityManager();

        $query = $em->createQuery(
            'SELECT p FROM App\Entity\Project p
            WHERE p.deliveryDate 
            BETWEEN :dateA AND :dateB
            AND p.status = :status');
        
        $query->setParameter('status', $status)
              ->setParameter('dateA', $dateA)
              ->setParameter('dateB', $dateB);


         return $query->getResult();
    }
    /**
     * PROJECTS faits vers API APIController
     */
    public function getCompletedProjectOrderedByDateAscAPI($date = "")
    {
        $status = "Fait";
        $videoStatus = 1;

        $entityManager = $this->getEntityManager();
        if ($date){

            $dateA = $date.'-01-01 00:00:00';
            $dateB = $date.'-12-31 00:00:00';


            $query = $entityManager->createQuery(
                'SELECT p.name, p.deliveryDate, p.pub_presentation, p.pub_video, c.name as cname, a.department, p.slug
            FROM App\Entity\Project p
            
            JOIN App\Entity\ProjectCategory c
            WITH p.category = c.id
             
            JOIN App\Entity\Applicant a
            WITH p.requestBy = a.id
             
            WHERE ( p.deliveryDate >= :dateA AND p.deliveryDate <= :dateB )
            AND ( p.status = :status )
            AND (p.pub_video_status = :videoStatus)
            
           
            ORDER BY p.deliveryDate ASC'
            )->setParameters([
                'dateA' => $dateA,
                'dateB' => $dateB,
                'status' => $status,
                'videoStatus' => $videoStatus

            ]);
            // returns an array of Product objects
            return $query->getResult();
        }
        else{
            $query = $entityManager->createQuery(
                'SELECT p.name, p.deliveryDate, p.pub_presentation, p.pub_video, c.name as cname, a.department, p.slug
            FROM App\Entity\Project p
            
            JOIN App\Entity\ProjectCategory c
            WITH p.category = c.id
             
            JOIN App\Entity\Applicant a
            WITH p.requestBy = a.id
          
            WHERE ( p.status = :status )
            AND (p.pub_video_status = :videoStatus)
            
            ORDER BY p.deliveryDate ASC'
            )->setParameters([

                'status' => $status,
                'videoStatus' => $videoStatus

            ]);
            // returns an array of Product objects
            return $query->getResult();

        }


    }
}
