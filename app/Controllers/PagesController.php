<?php

namespace Pornolizer\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class PagesController extends Controller
{
    public function home(Request $request, Response $response, array $args)
    {
        return $this->app->renderer->render($response, 'index.phtml', $args);
    }
}