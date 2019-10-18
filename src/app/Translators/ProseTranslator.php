<?php

namespace Pornolizer\Translators;


class ProseTranslator extends AbstractTranslator
{

    protected $lang = 'en';
    protected $adjectiveEndings = ['ing', 'ed', 's'];
    protected $nodeName = '';
    protected $weightMap = [
        'fallback' => 50,
        'p'        => 30,
        'h1'       => 90,
        'h2'       => 90,
        'h3'       => 80,
        'h4'       => 80,
        'h5'       => 70,
        'h6'       => 70,
    ];

    public function __construct(string $text, string $lang, string $nodeName)
    {
        $this->text = preg_split('/' . '\s+' . '/', $text);
        $this->nodeName = $nodeName;
        $this->lang = $lang;

        $words = file_get_contents(__DIR__ . '/dictionaries/' . $lang . '_adjectives.txt');
        $this->dictionary = explode("\n", $words);

        switch ($lang) {
            case 'en':
            default:
                $this->adjectiveEndings = ['ing', 'ed', 's'];
                break;
        }
    }

    public function translate()
    {
        $this->text = array_map(function ($word) {
            $wordEnding = $this->adjectiveMatcher($word);

            if ($wordEnding && $this->shouldTranslate()) {
                $word = $this->randomWord() . $wordEnding;
            }

            return $word;
        }, $this->text);

        return $this;
    }


    private function adjectiveMatcher($word)
    {
        preg_match('/' . '\w{3,10}(' . implode('|', $this->adjectiveEndings) . ')' . '/', $word, $matches);
        return isset($matches[1]) ? $matches[1] : false;
    }


}