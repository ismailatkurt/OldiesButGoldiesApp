<?php

namespace App\Contracts\Repositories;

use App\Entity\Record;
use App\Presenters\RecordsResult;
use Doctrine\ORM\ORMException;

interface RecordRepositoryInterface
{
    /**
     * @param int $page
     * @param int $limit
     * @param string|null $searchTerm
     * @param string|null $genre
     * @param string|null $description
     * @param string|null $publishedAt
     * @param string|null $artistName
     *
     * @return RecordsResult
     */
    public function all(
        int $page,
        int $limit,
        ?string $searchTerm = '',
        ?string $genre = '',
        ?string $description = '',
        ?string $publishedAt = '',
        ?string $artistName = ''
    ): RecordsResult;

    /**
     * @param Record $record
     *
     * @return Record|null
     *
     * @throws ORMException
     */
    public function save(Record $record): ?Record;

    /**
     * @param int $id
     *
     * @return Record|null
     */
    public function one(int $id): ?Record;

    /**
     * @param Record $record
     *
     * @return mixed
     */
    public function delete(Record $record);
}
