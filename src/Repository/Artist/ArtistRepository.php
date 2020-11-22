<?php

namespace App\Repository\Artist;

use App\Contracts\Repositories\ArtistRepositoryInterface;
use App\Entity\Artist;
use App\Presenters\ArtistsResult;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Artist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Artist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Artist[]    findAll()
 * @method Artist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtistRepository extends ServiceEntityRepository implements ArtistRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artist::class);
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string|null $searchTerm
     *
     * @return ArtistsResult
     */
    public function all(int $page, int $limit, ?string $searchTerm = ''): ArtistsResult
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

        return new ArtistsResult($pageCount, $page, $limit, $result);
    }

    /**
     * @param Artist $artist
     *
     * @throws ORMException
     */
    public function save(Artist $artist): void
    {
        $this->getEntityManager()->persist($artist);
        $this->getEntityManager()->flush();
    }

    /**
     * @return EntityManager|EntityManagerInterface
     */
    public function entityManager()
    {
        return $this->getEntityManager();
    }

    /**
     * @param int $id
     *
     * @return Artist|null
     */
    public function one(int $id): ?Artist
    {
        return $this->find($id);
    }
}
