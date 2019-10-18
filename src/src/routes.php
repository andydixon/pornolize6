<?php


// Routes

$app->get('/', \Pornolizer\Controllers\PagesController::class . ':home');
$app->get('/pornolize', \Pornolizer\Controllers\TranslationController::class . ':translate');
$app->get('/pornolize/', \Pornolizer\Controllers\TranslationController::class . ':translate');
$app->get('/proxy', \Pornolizer\Controllers\ProxyController::class . ':proxy');
$app->post('/api', \Pornolizer\Controllers\ApiController::class . ':api');



