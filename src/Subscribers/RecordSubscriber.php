<?php

namespace App\Subscribers;

use App\Adapters\Cache\RedisAdapter;
use App\Entity\Record;
use App\Events\Record\Deleted;
use App\Events\Record\Saved;
use App\Events\Record\Updated;
use App\Repository\Record\CacheRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RecordSubscriber extends AbstractSubscriber implements EventSubscriberInterface
{
    /**
     * @param RedisAdapter $cacheAdapter
     */
    public function __construct(RedisAdapter $cacheAdapter)
    {
        parent::__construct($cacheAdapter);
    }

    /**
     * @return array|string[]
     */
    public static function getSubscribedEvents()
    {
        return [
            Saved::NAME => 'onSavedAction',
            Updated::NAME => 'onUpdatedAction',
            Deleted::NAME => 'onDeletedAction',
        ];
    }

    /**
     * @param Saved $event
     */
    public function onSavedAction(Saved $event)
    {
        $this->deleteWithPattern(CacheRepository::RECORD_ALL_CACHE_KEY . '*');
    }

    /**
     * @param Updated $event
     */
    public function onUpdatedAction(Updated $event)
    {
        $this->deleteWithPattern(CacheRepository::RECORD_ALL_CACHE_KEY . '*');
        $this->cacheAdapter->del(CacheRepository::RECORD_SINGLE_CACHE_KEY . '_' . $event->getRecord()->getId());
    }

    /**
     * @param Deleted $event
     */
    public function onDeletedAction(Deleted $event)
    {
        $this->deleteWithPattern(CacheRepository::RECORD_ALL_CACHE_KEY . '*');
        $this->cacheAdapter->del(CacheRepository::RECORD_SINGLE_CACHE_KEY . '_' . $event->getRecord()->getId());
    }
}
