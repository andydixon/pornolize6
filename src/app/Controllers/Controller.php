<?php

namespace Pornolizer\Controllers;


use Psr\Container\ContainerInterface;

abstract class Controller
{
    protected $app;

    public function __construct(ContainerInterface $app)
    {
        $this->app = $app;
    }
}