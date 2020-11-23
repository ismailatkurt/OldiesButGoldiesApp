<?php

namespace App\Tests\Presenters;

use App\Presenters\RecordsResult;
use PHPUnit\Framework\TestCase;

class RecordsResultTest extends TestCase
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
        $records = [
            [
                'first' => 'record'
            ],
            [
                'second' => 'record'
            ]
        ];
        $expectedResult = [
            'totalPageCount' => $totalPageCount,
            'currentPage' => $currentPage,
            'countPerPage' => $countPerPage,
            'records' => $records,
        ];

        // test
        $classUnderTest = new RecordsResult($totalPageCount, $currentPage, $countPerPage, $records);
        $result = $classUnderTest->jsonSerialize();

        // assert
        $this->assertEquals($expectedResult, $result);
    }
}