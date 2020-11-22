<?php

namespace App\RequestFilters\Artist;

use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

class OneRequestFilter extends InputFilter
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        parent::init();

        $id = new Input('id');
        $id->setRequired(true);
        $this->add($id);
    }
}
