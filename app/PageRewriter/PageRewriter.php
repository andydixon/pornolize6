<?php

namespace Pornolizer\PageRewriter;

use Pornolizer\Translators\NameTranslator;
use Pornolizer\Translators\ProseTranslator;
use Wa72\HtmlPageDom\HtmlPageCrawler;

class PageRewriter
{
    protected $host;
    protected $selfHost;
    protected $response;
    protected $crawler;
    protected $url;
    public $encoding='utf-8';

    public function setHost($host)
    {
        $this->host = $host;
        return $this;
    }

    public function setSelfHost($selfHost)
    {
        $this->selfHost = $selfHost;
        return $this;
    }

    public function fetchPage($url)
    {
	$ua=$_SERVER['HTTP_USER_AGENT'];
	$this->response = \Httpful\Request::get($url)->addHeader('User-Agent',$ua)->followRedirects()->send();
	$this->url=$url;
        if ($this->response->content_type != 'text/html') {
            throw new \Exception('Unexpected content type: ' . $this->response->content_type);
        }

    $this->encoding = $this->response->charset;
	$this->crawler = new HtmlPageCrawler($this->response->body);

	// @FIXME: The Dom BS loses the DOCTYPE and <html /> so we sneak these into the returned object
	preg_match('/<html\b[^>]*>/i',$this->response->body,$htmlNode);
	preg_match('/<!DOCTYPE\b[^>]*>/i',$this->response->body,$htmlDocType);
 	$this->crawler->htmlNode = implode($htmlNode);
	$this->crawler->htmlDocType = implode($htmlDocType);
    }

    public function shouldRedirect()
    {
        $amp = $this->crawler->filter('link')
            ->reduce(function (HtmlPageCrawler $node) {
                return $node->attr('rel') === 'amphtml';
            })->first();

        if ($amp->count()) {
            return $amp->attr('href');
        }

        return false;
    }

    public function rewrite($lang)
    {
        $this->crawler->filter('p, h1, h2, h3, h4, h5, h6')->each(function (HtmlPageCrawler $c2) use ($lang) {

            $translator = NameTranslator::make($c2->text(), $lang, $c2->nodeName());
            $c2->text($translator->translate()->__toString());

            $translator = ProseTranslator::make($c2->text(), $lang, $c2->nodeName());
            $c2->text($translator->translate()->__toString());

            return $c2;
        });


        $this->crawler->filter('a')->each(function (HtmlPageCrawler $c2) use ($lang) {
            $c2->attr('rel', 'noreferrer nofollow');

            $href = $c2->attr('href');

            if ($href[0] === '/') {
                $c2->attr('href', $this->selfHost . '/translate?lang=' . $lang . '&url=' . $this->host . $c2->attr('href'));
            } else {
                $c2->attr('href', $this->selfHost . '/translate?lang=' . $lang . '&url=' . $c2->attr('href'));
            }

            return $c2;
	});


	$this->crawler->filter('link')->each(function (HtmlPageCrawler $c2) {
		$src = $c2->attr('href');
	        if ($src !== null) {
			if ($src[0] === '/' && $src[1] != '/') {
				$c2->attr('href',$this->host . $src);
			} else {
				$c2->attr('href',$src);
			}
		}
		return $c2;
	});

	$this->crawler->filter('img')->each(function (HtmlPageCrawler $c2) {
		$src = $c2->attr('src');
		if ($src !== null) {
			if ($src[0] === '/'  && $src[1] != '/') {
				$c2->attr('src',$this->host . $src);
			} else {
				$c2->attr('src',$src);
			}
		}      
	        $src = $c2->attr('srcset');
	        if ($src !== null) {
	                if ($src[0] === '/'  && $src[1] != '/') {
	                        $c2->attr('srcset',$this->host . $src);
	                } else {
	                        $c2->attr('srcset',$src);
	                }
	        }

		return $c2;
	}); 


        $injection = file_get_contents(__DIR__ . '/../inject.html');
        $this->crawler->filter('head')->append(
            '<base href="' . $this->host . '" />' .
            $injection
        );
//        $this->crawler->filter('body')->append(
//		'<img src="https://www.pornolize.com/ping?u='.urlencode(base64_encode(gzencode($this->url,9))).'&r='.urlencode(base64_encode(gzencode((!empty($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:''),9))).'">' 
//	);

        return $this->crawler;
    }

}
