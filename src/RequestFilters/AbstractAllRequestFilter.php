<?php

namespace App\RequestFilters;

use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator\GreaterThan;

abstract class AbstractAllRequestFilter extends InputFilter
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        parent::init();

        $page = new Input('page');
        $page->setFallbackValue(1);
        $greaterThanValidator = new GreaterThan(1);
        $page->getValidatorChain()->attach($greaterThanValidator);
        $this->add($page);

        $limit = new Input('limit');
        $limit->setFallbackValue(10);
        $greaterThanValidator = new GreaterThan(1);
        $limit->getValidatorChain()->attach($greaterThanValidator);
        $this->add($limit);

        $searchTerm = new Input('search-term');
        $searchTerm->setRequired(false);
        $this->add($searchTerm);
    }
}
