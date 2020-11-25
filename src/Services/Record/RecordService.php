<?php

namespace App\Services\Record;

use App\Contracts\Services\RecordServiceInterface;
use App\Entity\Record;
use App\Presenters\RecordsResult;
use App\Repository\Artist\ArtistRepository;
use App\Repository\Record\CacheRepository;
use DateTime;
use Doctrine\ORM\ORMException;

class RecordService implements RecordServiceInterface
{
    /**
     * @var CacheRepository
     */
    private CacheRepository $recordCacheRepository;

    /**
     * @var ArtistRepository
     */
    private ArtistRepository $artistRepository;

    /**
     * @param CacheRepository $recordCacheRepository
     * @param ArtistRepository $artistRepository
     */
    public function __construct(
        CacheRepository $recordCacheRepository,
        ArtistRepository $artistRepository
    ) {
        $this->recordCacheRepository = $recordCacheRepository;
        $this->artistRepository = $artistRepository;
    }

    /**
     * @param int $page
     * @param int $limit
     * @param string|null $searchTerm
     * @param string|null $genre
     * @param string|null $description
     * @param string|null $publishedAt
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
    ): RecordsResult
    {
        return $this->recordCacheRepository->all(
            $page,
            $limit,
            $searchTerm,
            $genre,
            $description,
            $publishedAt,
            $artistName
        );
    }

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
    ): ?Record {
        $record = new Record();
        $record->setName($name);
        $record->setGenre($genre);
        $record->setDescription($description);
        $record->setPublishedAt($publishedAt);

        $artist = $this->artistRepository->one($artistId);
        $record->setArtist($artist);

        return $this->recordCacheRepository->save($record);
    }

    /**
     * @param Record $record
     *
     * @return mixed|void
     * @throws ORMException
     */
    public function delete(Record $record)
    {
        $this->recordCacheRepository->delete($record);
    }

    /**
     * @param int $id
     *
     * @return Record|null
     */
    public function one(int $id): ?Record
    {
        return $this->recordCacheRepository->one($id);
    }

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
     *
     * @throws ORMException
     */
    public function update(
        int $id,
        string $name,
        int $artistId,
        ?string $genre = '',
        ?string $description = '',
        ?DateTime $publishedAt = null
    ): Record {
        $record = $this->one($id);

        $record->setName($name);
        $record->setGenre($genre);
        $record->setDescription($description);
        $record->setPublishedAt($publishedAt);

        $artist = $this->artistRepository->one($artistId);
        $record->setArtist($artist);

        return $this->recordCacheRepository->save($record);
    }
}
