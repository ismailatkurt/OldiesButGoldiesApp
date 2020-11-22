<?php

namespace App\RequestFilters\Record;

use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

class DeleteRequestFilter extends InputFilter
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
