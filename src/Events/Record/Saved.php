<?php

namespace App\Events\Record;

use App\Entity\Record;
use Symfony\Contracts\EventDispatcher\Event;

class Saved extends Event
{
    const NAME = 'record.saved';

    /**
     * @var Record
     */
    private Record $record;

    /**
     * Saved constructor.
     *
     * @param $record
     */
    public function __construct($record)
    {
        $this->record = $record;
    }

    /**
     * @return mixed
     */
    public function getRecord()
    {
        return $this->record;
    }
}
