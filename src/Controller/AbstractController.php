<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractController
{
    /**
     * @param string|null $queryString
     *
     * @return array
     */
    protected function getQueryParameters(?string $queryString = '')
    {
        $result = [];

        if ($queryString) {
            $queryParameters = explode('&', $queryString);
            if ($queryParameters) {
                foreach ($queryParameters as $queryParameter) {
                    [$key, $value] = explode('=', $queryParameter);
                    $result[$key] = $value;
                }
            }
        }

        return $result;
    }
}
