<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{

    private $passwordEncoder;

    public function __construct(ManagerRegistry $registry, UserPasswordEncoderInterface $userPasswordEncoderInterface)
    {
        parent::__construct($registry, User::class);
        $this->passwordEncoder = $userPasswordEncoderInterface;
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newEncodedPassword);
        $this->_em->persist($user);
        $this->_em->flush();
    }
    /**
     * Création d'un compte anonyme non utilisable
     */
    public function createJohnDoe()
    {
        
        $johnDoe = new User;

        $encrypt = $this->passwordEncoder->encodePassword($johnDoe, 'no_password');
        $johnDoe->setPassword($encrypt);

        $johnDoe
        ->setFirstname('John')
        ->setLastname('Doe')
        ->setEmail('john.doe@nowhere.fr')
        ->setRoles(['ROLE_OFF'])
        ->setJob('n/c')
        ->setMobil('/nc')
        ->setPhone('n/c')
        ->setOffice('n/c')
        ->setDepartment('n/c')
        ;

        $this->_em->persist($johnDoe);
        $this->_em->flush();

        return $johnDoe;
    }


    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return Project[]
     */
    public function findSearch(): array
    {
        return $this->findAll();
    }
}
