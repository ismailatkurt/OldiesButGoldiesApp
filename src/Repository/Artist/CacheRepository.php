<?php

namespace App\Repository\Artist;

use App\Adapters\RedisAdapter;
use App\Contracts\Repositories\ArtistRepositoryInterface;
use App\Entity\Artist;
use App\Presenters\ArtistsResult;
use Doctrine\ORM\ORMException;
use Psr\Cache\InvalidArgumentException;

class CacheRepository implements ArtistRepositoryInterface
{
    const ARTIST_ALL_CACHE_KEY = 'artists_all';
    const ARTIST_SINGLE_CACHE_KEY = 'artists_one';

    /**
     * @var RedisAdapter
     */
    private RedisAdapter $cacheAdapter;

    /**
     * @var ArtistRepository
     */
    private ArtistRepository $artistRepository;

    /**
     * @var RedisAdapter
     */
    private RedisAdapter $redisAdapter;

    /**
     * @param RedisAdapter $cacheAdapter
     * @param ArtistRepository $artistRepository
     * @param RedisAdapter $redisAdapter
     */
    public function __construct(
        RedisAdapter $cacheAdapter,
        ArtistRepository $artistRepository,
        RedisAdapter $redisAdapter
    ) {
        $this->cacheAdapter = $cacheAdapter;
        $this->artistRepository = $artistRepository;
        $this->redisAdapter = $redisAdapter;
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string|null $searchTerm
     *
     * @return ArtistsResult
     * @throws InvalidArgumentException
     */
    public function all(int $page, int $limit, ?string $searchTerm = ''): ArtistsResult
    {
        $artists = $this->cacheAdapter->get(
            self::ARTIST_ALL_CACHE_KEY . '_' . $page . '_' . $limit . '_' . $searchTerm
        );

        if (empty($artists)) {
            $artists = $this->artistRepository->all($page, $limit, $searchTerm);
            $this->cacheAdapter->set(
                self::ARTIST_ALL_CACHE_KEY . '_' . $page . '_' . $limit . '_' . $searchTerm,
                serialize($artists)
            );
        } else {
            $artists = unserialize($artists);
        }

        return $artists;
    }

    /**
     * @param Artist $artist
     *
     * @return Artist
     *
     * @throws ORMException
     */
    public function save(Artist $artist): Artist
    {
        return $this->artistRepository->save($artist);
    }

    /**
     * @param int $id
     *
     * @return Artist|null
     */
    public function one(int $id): ?Artist
    {
        return $this->artistRepository->one($id);

        $artist = $this->cacheAdapter->get(
            self::ARTIST_SINGLE_CACHE_KEY . '_' . $id
        );

        if (empty($artist)) {
            $artist = $this->artistRepository->one($id);
            $this->cacheAdapter->set(
                self::ARTIST_SINGLE_CACHE_KEY . '_' . $id,
                serialize($artist)
            );
        } else {
            $artist = unserialize($artist);
        }

        return $artist;
    }
}
