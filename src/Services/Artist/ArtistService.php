<?php

namespace App\Services\Artist;

use App\Contracts\Services\ArtistServiceInterface;
use App\Entity\Artist;
use App\Presenters\ArtistsResult;
use App\Repository\Artist\CacheRepository;
use Doctrine\ORM\ORMException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;

class ArtistService implements ArtistServiceInterface
{
    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * @var CacheRepository
     */
    private CacheRepository $artistCacheRepository;

    /**
     * @param CacheInterface $cache
     * @param CacheRepository $artistCacheRepository
     */
    public function __construct(
        CacheInterface $cache,
        CacheRepository $artistCacheRepository
    ) {
        $this->cache = $cache;
        $this->artistCacheRepository = $artistCacheRepository;
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string|null $searchTerm
     *
     * @return ArtistsResult
     *
     * @throws InvalidArgumentException
     */
    public function all(int $page, int $limit, ?string $searchTerm = ''): ArtistsResult
    {
        return $this->artistCacheRepository->all($page, $limit, $searchTerm);
    }

    /**
     * @param string $name
     *
     * @return Artist
     * @throws ORMException
     */
    public function create(string $name): Artist
    {
        $artist = new Artist();
        $artist->setName($name);

        return $this->artistCacheRepository->save($artist);
    }

    /**
     * @param int $id
     * @param string $name
     *
     * @return Artist
     *
     * @throws ORMException
     */
    public function update(int $id, string $name): Artist
    {
        $artist = $this->one($id);

        $artist->setName($name);

        return $this->artistCacheRepository->save($artist);
    }

    /**
     * @param int $id
     *
     * @return Artist|null
     */
    public function one(int $id): ?Artist
    {
        return $this->artistCacheRepository->one($id);
    }

    /**
     * @param Artist $artist
     *
     * @return mixed|void
     */
    public function delete(Artist $artist)
    {
        $this->artistCacheRepository->delete($artist);
    }
}
