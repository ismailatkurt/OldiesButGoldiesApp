<?php

namespace App\Events\Artist;

use App\Entity\Artist;
use Symfony\Contracts\EventDispatcher\Event;

class Deleted extends Event
{
    const NAME = 'artist.deleted';

    /**
     * @var Artist
     */
    private Artist $artist;

    /**
     * Saved constructor.
     *
     * @param $artist
     */
    public function __construct($artist)
    {
        $this->artist = $artist;
    }

    /**
     * @return mixed
     */
    public function getArtist()
    {
        return $this->artist;
    }
}
