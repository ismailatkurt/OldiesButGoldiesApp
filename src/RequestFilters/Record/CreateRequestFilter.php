<?php

namespace App\RequestFilters\Record;

use App\RequestFilters\AbstractCreateRequestFilter;
use Zend\InputFilter\Input;

class CreateRequestFilter extends AbstractCreateRequestFilter
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        parent::init();

        $artistId = new Input('artistId');
        $artistId->setRequired(true);
        $this->add($artistId);
    }
}
