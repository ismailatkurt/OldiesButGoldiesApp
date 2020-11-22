<?php

namespace App\Presenters;

use JsonSerializable;

class ArtistsResult implements JsonSerializable
{
    /**
     * @var int
     */
    private int $totalPageCount;

    /**
     * @var int
     */
    private int $currentPage;

    /**
     * @var int
     */
    private int $countPerPage;

    /**
     * @var array
     */
    private array $artists;

    /**
     * @param int $totalPageCount
     * @param int $currentPage
     * @param int $countPerPage
     * @param array $artists
     */
    public function __construct(int $totalPageCount, int $currentPage, int $countPerPage, array $artists)
    {
        $this->totalPageCount = $totalPageCount;
        $this->currentPage = $currentPage;
        $this->countPerPage = $countPerPage;
        $this->artists = $artists;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
