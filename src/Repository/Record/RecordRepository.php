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
     *
     * @return RecordsResult
     */
    public function all(int $page, int $limit, ?string $searchTerm = ''): RecordsResult
    {
        $queryBuilder = $this->createQueryBuilder('a');

        if (!empty($searchTerm)) {
            $queryBuilder->where('LOWER(a.name) like :searchTerm')
                ->setParameter('searchTerm', '%' . strtolower($searchTerm) . '%');
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
     * @throws ORMException
     */
    public function save(Record $record): void
    {
        $this->getEntityManager()->persist($record);
        $this->getEntityManager()->flush();
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
