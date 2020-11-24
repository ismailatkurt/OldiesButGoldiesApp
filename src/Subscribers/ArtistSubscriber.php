<?php

namespace App\Subscribers;

use App\Adapters\RedisAdapter;
use App\Entity\Artist;
use App\Entity\Record;
use App\Events\Artist\Saved;
use App\Events\Artist\Updated;
use App\Events\Artist\Deleted;
use App\Repository\Artist\CacheRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ArtistSubscriber extends AbstractSubscriber implements EventSubscriberInterface
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
        $this->deleteWithPattern(CacheRepository::ARTIST_ALL_CACHE_KEY . '*');
    }

    /**
     * @param Updated $event
     */
    public function onUpdatedAction(Updated $event)
    {
        $this->deleteWithPattern(CacheRepository::ARTIST_ALL_CACHE_KEY . '*');
        $this->cacheAdapter->del(Artist::class . '_one_' . $event->getArtist()->getId());
    }

    /**
     * @param Deleted $event
     */
    public function onDeletedAction(Deleted $event)
    {
        $this->deleteWithPattern(CacheRepository::ARTIST_ALL_CACHE_KEY . '*');
        $this->cacheAdapter->del(Artist::class . '_one_' . $event->getArtist()->getId());
    }
}
