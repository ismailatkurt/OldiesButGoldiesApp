<?php

namespace App\RequestFilters\Record;

use Zend\InputFilter\Input;

class UpdateRequestFilter extends CreateRequestFilter
{
    public function __construct()
    {
        parent::__construct();
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
