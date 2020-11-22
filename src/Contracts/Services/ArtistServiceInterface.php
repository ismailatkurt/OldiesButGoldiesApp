<?php

namespace App\Contracts\Services;

use App\Entity\Artist;
use App\Presenters\ArtistsResult;
use Doctrine\ORM\ORMException;

interface ArtistServiceInterface
{
    /**
     * @param int $page
     * @param int $limit
     * @param string|null $searchTerm
     *
     * @return ArtistsResult
     */
    public function all(int $page, int $limit, ?string $searchTerm): ArtistsResult;

    /**
     * @param string $name
     *
     * @return Artist
     *
     * @throws ORMException
     */
    public function create(string $name): Artist;

    /**
     * @param int $id
     *
     * @return Artist|null
     */
    public function one(int $id): ?Artist;
}
