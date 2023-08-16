<?php

namespace Nebula\Traits\Http;

use Nebula\Interfaces\Http\Response as HttpResponse;

trait Response
{
    public function response(int $code, string $content): HttpResponse
    {
        $response = app()->get(HttpResponse::class);
        $response->setStatusCode($code);
        $response->setContent($content);
        return $response;
    }
}
