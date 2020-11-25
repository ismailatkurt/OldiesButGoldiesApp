<?php

namespace App\Repository\Record;

use App\Contracts\Repositories\RecordRepositoryInterface;
use App\Entity\Record;
use App\Presenters\RecordsResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Record|null find($id, $lockMode = null, $lockVersion = null)
 * @method Record|null findOneBy(array $criteria, array $orderBy = null)
 * @method Record[]    findAll()
 * @method Record[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecordRepository extends ServiceEntityRepository implements RecordRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Record::class);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string|null $searchTerm
     * @param string|null $genre
     * @param string|null $description
     * @param string|null $publishedAt
     * @param string|null $artistName
     *
     * @return RecordsResult
     */
    public function all(
        int $page,
        int $limit,
        ?string $searchTerm = '',
        ?string $genre = '',
        ?string $description = '',
        ?string $publishedAt = '',
        ?string $artistName = ''
    ): RecordsResult {
        $queryBuilder = $this->createQueryBuilder('r')
            ->innerJoin('r.artist', 'art')
            ->addSelect('art');

        if (!empty($searchTerm)) {
            $queryBuilder->andWhere(
                $queryBuilder->expr()->orX(
                    $queryBuilder->expr()->like(
                        'LOWER(r.name)',
                        $queryBuilder->expr()->literal('%' . strtolower($searchTerm) . '%')
                    ),
                    $queryBuilder->expr()->like(
                        'LOWER(r.genre)',
                        $queryBuilder->expr()->literal('%' . strtolower($searchTerm) . '%')
                    ),
                    $queryBuilder->expr()->like(
                        'LOWER(r.description)',
                        $queryBuilder->expr()->literal('%' . strtolower($searchTerm) . '%')
                    )
                )
            );
//            $queryBuilder->andWhere('(LOWER(r.name)||LOWER(r.genre)||LOWER(r.description)) like :searchTerm')
//                ->setParameter('searchTerm', '%' . strtolower($searchTerm) . '%');
        }

        if (!empty($genre)) {
            $queryBuilder->andWhere('LOWER(r.genre) like :genre')
                ->setParameter('genre', '%' . strtolower($genre) . '%');
        }

        if (!empty($description)) {
            $queryBuilder->andWhere('LOWER(r.description) like :description')
                ->setParameter('description', '%' . strtolower($description) . '%');
        }

        if (!empty($publishedAt)) {
            $queryBuilder->andWhere('r.published_at = :publishedAt')
                ->setParameter('publishedAt', strtolower($publishedAt));
        }

        if (!empty($artistName)) {
            $queryBuilder->andWhere('LOWER(art.name) like :artistName')
                ->setParameter('artistName', '%' . strtolower($artistName) . '%');
        }

        $query = $queryBuilder->getQuery();

        $paginator = new Paginator($query);
        $paginatorCount = $paginator->count();

        $result = $paginator->getQuery()->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)->getArrayResult();

        $pageCount = ($paginatorCount / $limit) + 1;

        return new RecordsResult($pageCount, $page, $limit, $result);
    }

    /**
     * @param Record $record
     *
     * @return Record|null
     *
     * @throws ORMException
     */
    public function save(Record $record): ?Record
    {
        $record = $this->getEntityManager()->merge($record);
        $this->getEntityManager()->flush();

        return $record;
    }

    /**
     * @param int $id
     *
     * @return Record|null
     */
    public function one(int $id): ?Record
    {
        return $this->find($id);
    }

    /**
     * @param Record $record
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function delete(Record $record)
    {
        $record = $this->getEntityManager()->merge($record);
        $this->getEntityManager()->remove($record);
        $this->getEntityManager()->flush();
    }

    /**
     * @return EntityManager|EntityManagerInterface
     */
    public function entityManager()
    {
        return $this->getEntityManager();
    }
}
