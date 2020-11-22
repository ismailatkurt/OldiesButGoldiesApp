<?php

namespace App\Presenters;

use JsonSerializable;

class RecordsResult implements JsonSerializable
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
    private array $records;

    /**
     * @param int $totalPageCount
     * @param int $currentPage
     * @param int $countPerPage
     * @param array $records
     */
    public function __construct(int $totalPageCount, int $currentPage, int $countPerPage, array $records)
    {
        $this->totalPageCount = $totalPageCount;
        $this->currentPage = $currentPage;
        $this->countPerPage = $countPerPage;
        $this->records = $records;
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}
