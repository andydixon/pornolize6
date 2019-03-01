<?php

namespace Pornolizer\Controllers;

use Httpful\Exception\ConnectionErrorException;
use Pornolizer\Translators\EnglishTranslator;
use Slim\Http\Request;
use Slim\Http\Response;
use Pornolizer\Translators\NameTranslator;
use Pornolizer\Translators\ProseTranslator;

class ApiController extends Controller
{
    private $validLanguages = ['dk', 'de', 'en', 'es', 'hr', 'hu', 'no', 'se'];

    public function api(Request $request, Response $response, array $args)
    {
        $lang = $request->getParam('lang');
        $text = $request->getParam('text');

        if ($lang === null || $text === null || !in_array($lang, $this->validLanguages)) {
            $response->withHeader('Content-Type', 'text/plain');
            $response->write("Missing content or language");
        }

        try {
            $translator = NameTranslator::make($text, $lang, '');
            $text=$translator->translate()->__toString();

            $translator = ProseTranslator::make($text, $lang, '');
            $text = $translator->translate()->__toString();
        } catch (ConnectionErrorException $e) {
            $response->withHeader('Content-Type', 'text/plain');
            $response->write("Error");
        } catch (\Exception $e) {
            $response->withHeader('Content-Type', 'text/plain');
            $response->write("Error");
        }
        $response->withHeader('Content-Type', 'text/plain');
        $response->write(html_entity_decode(html_entity_decode($text))."\n");

    }
}
