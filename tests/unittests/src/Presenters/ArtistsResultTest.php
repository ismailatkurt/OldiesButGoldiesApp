<?php

namespace App\Tests\Presenters;

use App\Presenters\ArtistsResult;
use PHPUnit\Framework\TestCase;

class ArtistsResultTest extends TestCase
{
    /**
     * @test
     */
    public function jsonSerializeTest()
    {
        // prepare
        $totalPageCount = 123;
        $currentPage = 12;
        $countPerPage = 10;
        $artists = [
            [
                'first' => 'artist'
            ],
            [
                'second' => 'artist'
            ]
        ];
        $expectedResult = [
            'totalPageCount' => $totalPageCount,
            'currentPage' => $currentPage,
            'countPerPage' => $countPerPage,
            'artists' => $artists,
        ];

        // test
        $classUnderTest = new ArtistsResult($totalPageCount, $currentPage, $countPerPage, $artists);
        $result = $classUnderTest->jsonSerialize();

        // assert
        $this->assertEquals($expectedResult, $result);
    }
}