<?php

namespace App\RequestFilters\Record;

use App\RequestFilters\AbstractAllRequestFilter;
use Zend\Filter\StringTrim;
use Zend\Filter\StripTags;
use Zend\InputFilter\Input;
use Zend\Validator\Date;
use Zend\Validator\StringLength;

class AllRequestFilter extends AbstractAllRequestFilter
{
    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        parent::init();

        $genre = new Input('genre');
        $genre->setRequired(false);
        $genre->getFilterChain()->attach(new StringTrim());
        $genre->getValidatorChain()->attach(new StringLength(['max' => 255]));
        $this->add($genre);

        $description = new Input('description');
        $description->setRequired(false);
        $description->getFilterChain()->attach(new StringTrim());
        $description->getValidatorChain()->attach(new StringLength(['max' => 255]));
        $this->add($description);

        $publishedAt = new Input('publishedAt');
        $publishedAt->setRequired(false);
        $publishedAt->getFilterChain()->attach(new StringTrim());
        $publishedAt->getValidatorChain()->attach(new Date(['format' => 'Y-m-d']));
        $this->add($publishedAt);

        $artistId = new Input('artistName');
        $artistId->setRequired(false);
        $description->getFilterChain()->attach(new StringTrim());
        $description->getValidatorChain()->attach(new StringLength(['max' => 255]));
        $this->add($artistId);
    }
}
