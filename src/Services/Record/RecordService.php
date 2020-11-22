<?php

namespace App\Services\Record;

use App\Contracts\Services\RecordServiceInterface;
use App\Entity\Record;
use App\Presenters\RecordsResult;
use App\Repository\Artist\ArtistRepository;
use App\Repository\Record\CacheRepository;
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
     *
     * @return RecordsResult
     */
    public function all(int $page, int $limit, ?string $searchTerm = ''): RecordsResult
    {
        return $this->recordCacheRepository->all($page, $limit, $searchTerm);
    }

    /**
     * @param string $name
     * @param int $artistId
     *
     * @return Record
     * @throws ORMException
     */
    public function create(string $name, int $artistId): Record
    {
        $record = new Record();
        $record->setName($name);

        $artist = $this->artistRepository->one($artistId);
        $record->setArtist($artist);

        $this->recordCacheRepository->save($record);

        return $record;
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
}
