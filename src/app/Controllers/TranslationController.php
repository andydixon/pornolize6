<?php
namespace Pornolizer\Controllers;
use Httpful\Exception\ConnectionErrorException;
use Pornolizer\PageRewriter\PageRewriter;
use Pornolizer\Translators\EnglishTranslator;
use Slim\Http\Request;
use Slim\Http\Response;

class TranslationController extends Controller
{
    private $validLanguages = ['dk', 'de', 'en', 'es', 'hr', 'hu', 'no', 'se'];

    public function translate(Request $request, Response $response, array $args)
    {    
	//var_dump($_SERVER);die();
	if(empty($_SESSION['pornolizer'])) {
		header("Location: http://www.pornolize.com/");      
		die();
	}


        $lang = $request->getParam('lang');
        $url = $request->getParam('url');

        if ($lang === null || $url === null || $url === 'http://' || !in_array($lang, $this->validLanguages)) {
            return $response->withRedirect('/');
        }

	$host = parse_url($url);
	$hostname=$host['host'];
        $host = $host['scheme'] . '://' . $host['host'];

        $selfHost = $request->getUri()->getScheme() . '://' . $request->getUri()->getHost() .
            ($request->getUri()->getPort() !== 80 ? ':' . $request->getUri()->getPort() : '');


        $pageRewriter = (new PageRewriter())->setHost($host)->setSelfHost($selfHost);
        //	$tmp = explode(',', isset($_SERVER['HTTP_CLIENT_IP'])?$_SERVER['HTTP_CLIENT_IP']:isset($_SERVER['HTTP_X_FORWARDED_FOR'])?$_SERVER['HTTP_X_FORWARDED_FOR']:$_SERVER['REMOTE_ADDR']); $ip = trim(end($tmp));
        //	$data=["timestamp"=>time().'000',
        //		"url"=>$request->getParam('url'),
        //		"domain"=>$hostname,
        //		"ip"=>$ip,
        //		"ua"=>$_SERVER['HTTP_USER_AGENT']??'unknown'
        //	];
        //
        //	$payload=json_encode($data);
        //	$ch = curl_init('http://172.17.0.1:9200/pornolize/_doc');
        //	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        //		'Content-Type: application/json',
        //		'Content-Length: ' . strlen($payload))
        //	);
        //
        //	curl_setopt($ch, CURLOPT_POSTFIELDS, $payload );
        //	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        //	$res = curl_exec($ch);
        //	curl_close($ch);
        try {
            $pageRewriter->fetchPage($url);
        } catch (ConnectionErrorException $e) {
            return $this->app->renderer->render($response, 'index.phtml', ['error' => 'Oh no it\'s all gone to shit.']);
        } catch (\Exception $e) {
            return $this->app->renderer->render($response, 'index.phtml', ['error' => $e->getMessage()]);
        }

        // amp redirect - disabled for now

//        $redirect = $pageRewriter->shouldRedirect();

//        if ($redirect) {
//            return $response->withRedirect('/translate?lang=en&url=' . $redirect);
//        }


        $rewrite = $pageRewriter->rewrite($lang);
        $newResponse = $response->withHeader('Content-Type', 'text/html; charset='.$pageRewriter->encoding);

        $newResponse->write($rewrite->html());
        return $newResponse;
    }
}
