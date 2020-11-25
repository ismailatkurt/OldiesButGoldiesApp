<?php

namespace App\Contracts\Services;

use App\Entity\Record;
use App\Presenters\RecordsResult;
use DateTime;
use Doctrine\ORM\ORMException;

interface RecordServiceInterface
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
        ?string $searchTerm,
        ?string $genre = '',
        ?string $description = '',
        ?string $publishedAt = '',
        ?string $artistName = ''
    ): RecordsResult;

    /**
     * @param string $name
     * @param int $artistId
     * @param string|null $genre
     * @param string|null $description
     * @param DateTime|null $publishedAt
     *
     * @return Record
     *
     * @throws ORMException
     */
    public function create(
        string $name,
        int $artistId,
        ?string $genre = '',
        ?string $description = '',
        ?DateTime $publishedAt = null
    ): ?Record;

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

    /**
     * @param int $id
     * @param string $name
     * @param int $artistId
     *
     * @param string|null $genre
     * @param string|null $description
     * @param DateTime|null $publishedAt
     *
     * @return Record
     */
    public function update(
        int $id,
        string $name,
        int $artistId,
        ?string $genre = '',
        ?string $description = '',
        ?DateTime $publishedAt = null
    ): Record;
}
