<?php

namespace App\Entity;

use App\Repository\ArtistRepository;

class ArtistFactory
{
    /**
     * @var ArtistRepository
     */
    private ArtistRepository $artistRepository;

    /**
     * @param ArtistRepository $artistRepository
     */
    public function __construct(ArtistRepository $artistRepository)
    {
        $this->artistRepository = $artistRepository;
    }

    public function createArtistsFromArray(array $artistsArr)
    {
        $artists = [];

        return $artists;
    }
}