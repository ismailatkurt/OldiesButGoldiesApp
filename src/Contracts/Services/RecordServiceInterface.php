<?php

namespace App\Contracts\Services;

use App\Entity\Record;
use App\Presenters\RecordsResult;
use Doctrine\ORM\ORMException;

interface RecordServiceInterface
{
    /**
     * @param int $page
     * @param int $limit
     * @param string|null $searchTerm
     *
     * @return RecordsResult
     */
    public function all(int $page, int $limit, ?string $searchTerm): RecordsResult;

    /**
     * @param string $name
     * @param int $artistId
     *
     * @return Record
     *
     * @throws ORMException
     */
    public function create(string $name, int $artistId): Record;

    /**
     * @param Record $record
     *
     * @return mixed
     */
    public function delete(Record $record);

    /**
     * @param int $id
     *
     * @return Record|null
     */
    public function one(int $id): ?Record;
}
