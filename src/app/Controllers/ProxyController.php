<?php

namespace Pornolizer\Controllers;


use Slim\Http\Request;
use Slim\Http\Response;

class ProxyController
{
    public function proxy(Request $request, Response $response)
    {
        $url = $request->getParam('url');

        if ($url === null) {
            return $response->withRedirect('/');
        }

        $proxyData = \Httpful\Request::get($url)->followRedirects()->send();

        $newResponse = $response->withHeader('Content-type', $proxyData->content_type);
        $newResponse->write($proxyData->raw_body);
        return $newResponse;
    }
}