<?php

namespace App\Repository;

use App\Entity\BlogWeb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BlogWeb|null find($id, $lockMode = null, $lockVersion = null)
 * @method BlogWeb|null findOneBy(array $criteria, array $orderBy = null)
 * @method BlogWeb[]    findAll()
 * @method BlogWeb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlogWebRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BlogWeb::class);
    }

    // /**
    //  * @return BlogWeb[] Returns an array of BlogWeb objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BlogWeb
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
