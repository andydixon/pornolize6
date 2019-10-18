<?php

namespace Pornolizer\Translators;


abstract class AbstractTranslator
{
    protected $text = [];
    protected $lang = '';
    protected $nodeName = '';
    protected $dictionary = [];
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

    public static function make(string $text, string $lang, string $nodeName)
    {
        return new static($text, $lang, $nodeName);
    }

    abstract public function translate();

    public function __toString()
    {
        return htmlspecialchars(implode(' ', $this->text));
    }

    protected function randomWord()
    {
        return $this->dictionary[mt_rand(0, count($this->dictionary) - 1)];
    }

    protected function shouldTranslate()
    {
        return mt_rand(1, 100) <= (isset($this->weightMap[$this->nodeName]) ? $this->weightMap[$this->nodeName] : $this->weightMap['fallback']);
    }
}