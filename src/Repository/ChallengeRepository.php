<?php

namespace App\Repository;

use App\Entity\Challenge;
use App\Entity\ChallengeSearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use DateTime;

/**
 * @method Challenge|null find($id, $lockMode = null, $lockVersion = null)
 * @method Challenge|null findOneBy(array $criteria, array $orderBy = null)
 * @method Challenge[]    findAll()
 * @method Challenge[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChallengeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Challenge::class);
    }

    public function findAllByDateQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->where('c.dateStart >= :date')
            ->setParameter(':date', (new DateTime())->setTime(0, 0, 0))
            ->orderBy('c.dateStart', 'ASC');
    }

    public function findAllByDateAdminQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.dateStart', 'ASC');
    }

    /**
     * @param string|null $slug
     * @return QueryBuilder
     */
    public function findBySportQueryBuilder(?string $slug): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->join('c.sports', 's')
            ->where('c.dateStart >= :date')
            ->setParameter(':date', (new DateTime())->setTime(0, 0, 0))
            ->andWhere('s.slug = :slug')
            ->setParameter('slug', $slug)
            ->orderBy('c.dateStart', 'ASC');
    }

    /**
     * @param string|null $name
     * @return QueryBuilder
     */
    public function findByCategoryQueryBuilder(?string $name): QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->join('c.sports', 's')
            ->join('s.category', 'cat')
            ->where('c.dateStart >= :date')
            ->setParameter(':date', (new DateTime())->setTime(0, 0, 0))
            ->andWhere('cat.name = :name')
            ->setParameter('name', $name)
            ->orderBy('c.dateStart', 'ASC');
    }

    /**
     * @param ChallengeSearch $search
     * @return QueryBuilder
     */
    public function searchChallenges(ChallengeSearch $search): QueryBuilder
    {
        $query = $this->createQueryBuilder('c')
            ->andWhere('c.dateStart >= :today');
        $parameters = [];
        $parameters['today'] = (new DateTime())->setTime(0, 0, 0);
        if ($search->getDateStart()) {
            /* @phpstan-ignore-next-line */
            $dates = explode(' - ', $search->getDateStart());
            $date1 = trim($dates[0]);
            $date2 = trim($dates[1]);
            $date1 = explode('/', $date1);
            $dateStart = date($date1[2] . '-' . $date1[1] . '-' . $date1[0]);
            $date2 = explode('/', $date2);
            $dateEnd = date($date2[2] . '-' . $date2[1] . '-' . $date2[0]);
            $dateEnd .= ' 23:59:59';
            $query = $query
                ->andWhere('c.dateStart >= :dateStart')
                ->andWhere('c.dateStart <= :dateEnd');
            $parameters['dateStart'] = $dateStart;
            $parameters['dateEnd'] = $dateEnd;
        }
        if ($search->getSport()) {
            $query = $query
                ->join('c.sports', 's')
                ->andWhere('s.id = :sport');
            $parameters['sport'] = $search->getSport();
        }
        if ($search->getDistance()) {
            /* @phpstan-ignore-next-line */
            $distances = explode('-', $search->getDistance());

            $min = (int)$distances[0];
            $max = (int)$distances[1];

            $query = $query
                ->andWhere('c.distance >= :minDistance')
                ->andWhere('c.distance <= :maxDistance');
            $parameters['minDistance'] = $min;
            $parameters['maxDistance'] = $max;
        }
        if ($search->getParticipants()) {
            /* @phpstan-ignore-next-line */
            $participants = explode(' - ', $search->getParticipants());
            $min = (int)$participants[0];
            $max = (int)$participants[1];
            $query = $query
                ->leftjoin('c.participants', 'u')
                ->groupBy('c.id')
                ->andHaving('COUNT(distinct u.id) >= :minParticipants')
                ->andHaving('COUNT(distinct u.id) <= :maxParticipants');
            $parameters['minParticipants'] = $min;
            $parameters['maxParticipants'] = $max;
        }
        return $query
            ->orderBy('c.dateStart', 'ASC')
            ->setParameters($parameters);
    }

    /**
     * @return array<string>
     */
    public function getChallengeBySports(): array
    {
        return $this->createQueryBuilder('c')
            ->select('s.name, s.color, count(c.id) as challengeCount')
            ->join('c.sports', 's')
            ->where('c.dateStart >= :date')
            ->setParameter(':date', new DateTime())
            ->orderBy('c.dateStart', 'ASC')
            ->groupBy('s.name, s.color')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function maxParticipants()
    {
        $query = $this->createQueryBuilder('c')
            ->select('count(u.id) as maximum')
            ->leftJoin('c.participants', 'u')
            ->groupBy('c.id')
            ->orderBy('maximum', 'DESC')
            ->setMaxResults(1);

        $maxParticipants = $query->getQuery()->getOneOrNullResult();
        if ($maxParticipants) {
            return $query->getQuery()->getSingleScalarResult();
        } else {
            return 0;
        }
    }

    /**
     * @return int|mixed|string
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function minParticipants()
    {
        $query = $this->createQueryBuilder('c')
            ->select('count(u.id) as minimum')
            ->leftJoin('c.participants', 'u')
            ->groupBy('c.id')
            ->orderBy('minimum', 'ASC')
            ->setMaxResults(1);

        $minParticipants = $query->getQuery()->getOneOrNullResult();
        if ($minParticipants) {
            return $query->getQuery()->getSingleScalarResult();
        } else {
            return 0;
        }
    }

    /**
     * @return Challenge[]
     */
    public function minMaxDistance(): array
    {
        return $this->createQueryBuilder('c')
            ->select('MIN(c.distance) as minDistance, MAX(c.distance) as maxDistance')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string $title
     * @param int $limit
     * @return array<string>
     */
    public function findLikeTitle(string $title, int $limit = 5): array
    {
        $queryBuilder = $this->createQueryBuilder('c')
            ->where('c.title LIKE :title')
            ->setParameter('title', '%' . $title . '%')
            ->orderBy('c.title', 'ASC')
            ->setMaxResults($limit)
            ->getQuery();

        return $queryBuilder->getResult();
    }
}
