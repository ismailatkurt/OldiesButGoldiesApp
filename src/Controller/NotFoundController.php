<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class NotFoundController
{
    /**
     * @return Response
     */
    public function show()
    {
        return new Response('', 404);
    }
}
