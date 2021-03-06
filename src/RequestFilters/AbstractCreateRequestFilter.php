<?php

namespace App\RequestFilters;

use Zend\Filter\StringTrim;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;
use Zend\Validator\StringLength;

abstract class AbstractCreateRequestFilter extends InputFilter
{
    public function init()
    {
        parent::init();

        $name = new Input('name');
        $name->getFilterChain()->attach(new StringTrim());
        $name->getValidatorChain()->attach(new NotEmpty());
        $name->getValidatorChain()->attach(new StringLength(['max' => 255]));
        $this->add($name);
    }
}
