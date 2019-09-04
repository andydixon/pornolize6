<?php

namespace Pornolizer\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class PagesController extends Controller
{
    public function home(Request $request, Response $response, array $args)
    {
	    $_SESSION['pornolizer']=sha1(date('U').microtime(true));    
	    return $this->app->renderer->render($response, 'index.phtml', $args);
    }
}
