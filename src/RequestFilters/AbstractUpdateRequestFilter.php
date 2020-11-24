<?php

namespace App\RequestFilters;

use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

abstract class AbstractUpdateRequestFilter extends InputFilter
{
    public function init()
    {
        parent::init();

        $id = new Input('id');
        $id->setRequired(true);
        $this->add($id);
    }
}
