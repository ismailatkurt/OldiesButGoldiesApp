<?php

namespace App\Repository\Record;

use App\Adapters\RedisAdapter;
use App\Contracts\Repositories\RecordRepositoryInterface;
use App\Entity\Artist;
use App\Entity\Record;
use App\Presenters\RecordsResult;
use Doctrine\ORM\ORMException;
use Symfony\Component\Serializer\SerializerInterface;

class CacheRepository implements RecordRepositoryInterface
{
    const RECORD_ALL_CACHE_KEY = 'records_all';

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
     *
     * @return RecordsResult
     */
    public function all(int $page, int $limit, ?string $searchTerm = ''): RecordsResult
    {
        $records = $this->cacheAdapter->get(
            self::RECORD_ALL_CACHE_KEY . '_' . $page . '_' . $limit . '_' . $searchTerm
        );

        if (empty($records)) {
            $records = $this->recordRepository->all($page, $limit, $searchTerm);
            $this->cacheAdapter->set(
                self::RECORD_ALL_CACHE_KEY . '_' . $page . '_' . $limit . '_' . $searchTerm,
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
     * @throws ORMException
     */
    public function save(Record $record): void
    {
        $this->recordRepository->save($record);
    }

    /**
     * @param int $id
     *
     * @return Record|null
     */
    public function one(int $id): ?Record
    {
        return $this->recordRepository->one($id);
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
