<?php

namespace App\Contracts\Repositories;

use App\Entity\Artist;
use App\Presenters\ArtistsResult;
use Doctrine\ORM\ORMException;

interface ArtistRepositoryInterface
{
    /**
     * @param int $page
     * @param int $limit
     * @param string|null $searchTerm
     *
     * @return ArtistsResult
     */
    public function all(int $page, int $limit, ?string $searchTerm = ''): ArtistsResult;

    /**
     * @param Artist $artist
     *
     * @return Artist
     *
     * @throws ORMException
     */
    public function save(Artist $artist): Artist;

    /**
     * @param int $id
     *
     * @return Artist|null
     */
    public function one(int $id): ?Artist;
}
