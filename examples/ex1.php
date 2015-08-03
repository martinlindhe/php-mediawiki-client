<?php

require_once __DIR__.'/../vendor/autoload.php';

$res = (new MartinLindhe\MediawikiClient\Client)
    ->server('en.wikipedia.org')
    ->cacheTtlSeconds(3600 * 24 * 365) // 1 year
    ->fetchArticle('Eddie Meduza');

d($res);
