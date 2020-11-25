<?php

namespace App\Repository\Record;

use App\Adapters\Cache\RedisAdapter;
use App\Contracts\Repositories\RecordRepositoryInterface;
use App\Entity\Record;
use App\Presenters\RecordsResult;
use Doctrine\ORM\ORMException;
use Symfony\Component\Serializer\SerializerInterface;

class CacheRepository implements RecordRepositoryInterface
{
    const RECORD_ALL_CACHE_KEY = 'records_all';
    const RECORD_SINGLE_CACHE_KEY = 'records_one';

    /**
     * @var RecordRepository
     */
    private RecordRepository $recordRepository;

    /**
     * @var SerializerInterface
     */
    private SerializerInterface $serializer;

    /**
     * @var RedisAdapter
     */
    private RedisAdapter $cacheAdapter;

    /**
     * @param RedisAdapter $cacheAdapter
     * @param RecordRepository $recordRepository
     * @param SerializerInterface $serializer
     */
    public function __construct(
        RedisAdapter $cacheAdapter,
        RecordRepository $recordRepository,
        SerializerInterface $serializer
    ) {
        $this->cacheAdapter = $cacheAdapter;
        $this->recordRepository = $recordRepository;
        $this->serializer = $serializer;
    }

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
    ): RecordsResult {
        $cacheKeyPostfix = implode(
            '_',
            [$page, $limit, $searchTerm, $genre, $description, $publishedAt, $artistName]
        );
        $records = $this->cacheAdapter->get(
            self::RECORD_ALL_CACHE_KEY . '_' . $cacheKeyPostfix
        );

        if (empty($records)) {
            $records = $this->recordRepository->all(
                $page,
                $limit,
                $searchTerm,
                $genre,
                $description,
                $publishedAt,
                $artistName
            );
            $this->cacheAdapter->set(
                self::RECORD_ALL_CACHE_KEY . '_' . $cacheKeyPostfix,
                serialize($records)
            );
        } else {
            $records = unserialize($records);
        }

        return $records;
    }

    /**
     * @param Record $record
     *
     * @return Record|null
     *
     * @throws ORMException
     */
    public function save(Record $record): ?Record
    {
        return $this->recordRepository->save($record);
    }

    /**
     * @param int $id
     *
     * @return Record|null
     */
    public function one(int $id): ?Record
    {
        $record = $this->cacheAdapter->get(
            self::RECORD_SINGLE_CACHE_KEY . '_' . $id
        );

        if (empty($record)) {
            $record = $this->recordRepository->one($id);
            $this->cacheAdapter->set(
                self::RECORD_SINGLE_CACHE_KEY . '_' . $id,
                serialize($record)
            );
        } else {
            $record = unserialize($record);
        }

        return $record;
    }

    /**
     * @param Record $record
     *
     * @throws ORMException
     */
    public function delete(Record $record)
    {
        $this->recordRepository->delete($record);
    }
}
