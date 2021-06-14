<?php

namespace App\Repository;

use App\Entity\CatchPhrase;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CatchPhrase|null find($id, $lockMode = null, $lockVersion = null)
 * @method CatchPhrase|null findOneBy(array $criteria, array $orderBy = null)
 * @method CatchPhrase[]    findAll()
 * @method CatchPhrase[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CatchPhraseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CatchPhrase::class);
    }

    // /**
    //  * @return CatchPhrase[] Returns an array of CatchPhrase objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?CatchPhrase
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
